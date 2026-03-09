<?php

namespace App\Http\Controllers;

use App\Services\SystemHealthMetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AdminSystemHealthController extends Controller
{
    public function __construct(private SystemHealthMetricsService $systemHealthMetricsService) {}

    public function index(): View
    {
        return view('admin.system_health.index', [
            'titlePage' => 'System Health',
            'breadcrumbs' => [
                ['link' => route('dashboard'), 'name' => 'Dashboard'],
                ['name' => 'System Health', 'active' => true],
            ],
            'healthSnapshot' => $this->systemHealthMetricsService->snapshot(),
        ]);
    }

    public function snapshot(): JsonResponse
    {
        return response()->json($this->systemHealthMetricsService->snapshot());
    }
}
