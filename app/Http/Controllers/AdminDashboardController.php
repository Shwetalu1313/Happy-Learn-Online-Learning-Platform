<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollUser;
use App\Models\CurrencyExchange;
use App\Models\SystemActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminDashboardController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $payload = $this->buildDashboardPayload(365);

        return view('dashboard', [
            'titlePage' => __('nav.dashboard'),
            'breadcrumbs' => [
                ['link' => route('dashboard'), 'name' => 'Dashboard', 'active' => true],
            ],
            ...$payload,
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $days = $this->normalizeDays((int) $request->query('days', 30));
        $payload = $this->buildDashboardPayload(365);

        $labels = $payload['chartData']['labels'];
        $enrollments = $payload['chartData']['enrollments'];
        $income = $payload['chartData']['income'];
        $registrations = $payload['chartData']['registrations'];

        $sliceStart = max(0, count($labels) - $days);

        $filename = 'admin-dashboard-report-'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($payload, $labels, $enrollments, $income, $registrations, $sliceStart) {
            $output = fopen('php://output', 'w');

            fputcsv($output, ['Section', 'Metric', 'Value']);
            fputcsv($output, ['Summary', 'Generated At', now()->toDateTimeString()]);
            fputcsv($output, ['Summary', 'Enrollments (Current Month)', $payload['metrics']['enrollments']['current']]);
            fputcsv($output, ['Summary', 'Enrollments (Previous Month)', $payload['metrics']['enrollments']['previous']]);
            fputcsv($output, ['Summary', 'Enrollments Change %', $payload['metrics']['enrollments']['change']]);
            fputcsv($output, ['Summary', 'Income MMK (Current Month)', $payload['metrics']['income']['current_mmk']]);
            fputcsv($output, ['Summary', 'Income MMK (Previous Month)', $payload['metrics']['income']['previous_mmk']]);
            fputcsv($output, ['Summary', 'Income Change %', $payload['metrics']['income']['change']]);
            fputcsv($output, ['Summary', 'Registrations (Current Month)', $payload['metrics']['registrations']['current']]);
            fputcsv($output, ['Summary', 'Registrations (Previous Month)', $payload['metrics']['registrations']['previous']]);
            fputcsv($output, ['Summary', 'Registrations Change %', $payload['metrics']['registrations']['change']]);

            fputcsv($output, []);
            fputcsv($output, ['Date', 'Enrollments', 'Income_MMK', 'Registrations']);

            for ($i = $sliceStart; $i < count($labels); $i++) {
                fputcsv($output, [
                    $labels[$i],
                    $enrollments[$i],
                    $income[$i],
                    $registrations[$i],
                ]);
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function printView(Request $request, string $paper = 'A4'): \Illuminate\View\View
    {
        $paper = strtoupper($paper);
        if (! in_array($paper, ['A5', 'A4', 'A3', 'A1'], true)) {
            $paper = 'A4';
        }

        $days = $this->normalizeDays((int) $request->query('days', 30));
        $payload = $this->buildDashboardPayload(365);

        $labels = $payload['chartData']['labels'];
        $enrollments = $payload['chartData']['enrollments'];
        $income = $payload['chartData']['income'];
        $registrations = $payload['chartData']['registrations'];

        $sliceStart = max(0, count($labels) - $days);
        $timelineRows = [];

        for ($i = $sliceStart; $i < count($labels); $i++) {
            $timelineRows[] = [
                'date' => $labels[$i],
                'enrollments' => $enrollments[$i],
                'income' => $income[$i],
                'registrations' => $registrations[$i],
            ];
        }

        return view('admin.dashboard_print', [
            ...$payload,
            'timelineRows' => $timelineRows,
            'paper' => $paper,
            'days' => $days,
            'generatedAt' => now(),
        ]);
    }

    private function buildDashboardPayload(int $timelineDays): array
    {
        $timelineDays = max(30, min($timelineDays, 365));
        $now = Carbon::now();

        $currentMonthStart = $now->copy()->startOfMonth();
        $previousMonthStart = $currentMonthStart->copy()->subMonthNoOverflow();
        $previousMonthEnd = $currentMonthStart->copy()->subSecond();

        $currentEnrollments = CourseEnrollUser::whereBetween('created_at', [$currentMonthStart, $now])->count();
        $previousEnrollments = CourseEnrollUser::whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])->count();

        $currentIncomeMmk = (int) CourseEnrollUser::whereBetween('created_at', [$currentMonthStart, $now])->sum('amount');
        $previousIncomeMmk = (int) CourseEnrollUser::whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])->sum('amount');

        $currentRegistrations = User::whereBetween('created_at', [$currentMonthStart, $now])->count();
        $previousRegistrations = User::whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])->count();

        $usdRateValue = CurrencyExchange::query()->value('us_ex');
        $usdRate = max(1, (int) ($usdRateValue ?? 1));

        $seriesStart = $now->copy()->subDays($timelineDays - 1)->startOfDay();
        $seriesEnd = $now->copy()->endOfDay();

        $enrollmentRows = CourseEnrollUser::query()
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->whereBetween('created_at', [$seriesStart, $seriesEnd])
            ->groupBy('d')
            ->pluck('c', 'd')
            ->toArray();

        $incomeRows = CourseEnrollUser::query()
            ->selectRaw('DATE(created_at) as d, COALESCE(SUM(amount), 0) as c')
            ->whereBetween('created_at', [$seriesStart, $seriesEnd])
            ->groupBy('d')
            ->pluck('c', 'd')
            ->toArray();

        $registrationRows = User::query()
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->whereBetween('created_at', [$seriesStart, $seriesEnd])
            ->groupBy('d')
            ->pluck('c', 'd')
            ->toArray();

        $labels = [];
        $enrollmentSeries = [];
        $incomeSeries = [];
        $registrationSeries = [];

        $cursor = $seriesStart->copy();
        while ($cursor->lte($seriesEnd)) {
            $day = $cursor->toDateString();
            $labels[] = $day;
            $enrollmentSeries[] = (int) ($enrollmentRows[$day] ?? 0);
            $incomeSeries[] = (int) ($incomeRows[$day] ?? 0);
            $registrationSeries[] = (int) ($registrationRows[$day] ?? 0);
            $cursor->addDay();
        }

        return [
            'metrics' => [
                'enrollments' => [
                    'current' => $currentEnrollments,
                    'previous' => $previousEnrollments,
                    'change' => $this->calculatePercentChange($currentEnrollments, $previousEnrollments),
                    'trend' => $currentEnrollments >= $previousEnrollments ? 'up' : 'down',
                ],
                'income' => [
                    'current_mmk' => $currentIncomeMmk,
                    'previous_mmk' => $previousIncomeMmk,
                    'current_usd' => round($currentIncomeMmk / $usdRate, 2),
                    'previous_usd' => round($previousIncomeMmk / $usdRate, 2),
                    'change' => $this->calculatePercentChange($currentIncomeMmk, $previousIncomeMmk),
                    'trend' => $currentIncomeMmk >= $previousIncomeMmk ? 'up' : 'down',
                ],
                'registrations' => [
                    'current' => $currentRegistrations,
                    'previous' => $previousRegistrations,
                    'change' => $this->calculatePercentChange($currentRegistrations, $previousRegistrations),
                    'trend' => $currentRegistrations >= $previousRegistrations ? 'up' : 'down',
                ],
            ],
            'chartData' => [
                'labels' => $labels,
                'enrollments' => $enrollmentSeries,
                'income' => $incomeSeries,
                'registrations' => $registrationSeries,
            ],
            'latestActivities' => SystemActivity::getData(false, true),
        ];
    }

    private function normalizeDays(int $days): int
    {
        if (! in_array($days, [7, 30, 90, 180, 365], true)) {
            return 30;
        }

        return $days;
    }

    private function calculatePercentChange(float|int $current, float|int $previous): float
    {
        if ((float) $previous === 0.0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 2);
    }
}
