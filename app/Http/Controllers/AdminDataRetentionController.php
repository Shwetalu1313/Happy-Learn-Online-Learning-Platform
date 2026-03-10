<?php

namespace App\Http\Controllers;

use App\Models\DataPurgeRun;
use App\Services\DataPurgeService;
use App\Services\RetentionPolicyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminDataRetentionController extends Controller
{
    public function __construct(
        private readonly RetentionPolicyService $retentionPolicyService,
        private readonly DataPurgeService $dataPurgeService
    ) {}

    public function index(): View
    {
        $titlePage = 'Data Retention & Purge';
        $targets = $this->retentionPolicyService->targets();
        $policies = $this->retentionPolicyService->all()->keyBy('target_key');
        $runs = DataPurgeRun::query()->orderByDesc('id')->limit(20)->get();

        return view('admin.data_retention.index', compact('titlePage', 'targets', 'policies', 'runs'));
    }

    public function updatePolicies(Request $request): RedirectResponse
    {
        $rules = [
            'policies' => ['required', 'array'],
        ];

        foreach (array_keys($this->retentionPolicyService->targets()) as $targetKey) {
            $rules["policies.{$targetKey}.keep_days"] = ['required', 'integer', 'min:1', 'max:3650'];
            $rules["policies.{$targetKey}.archive_grace_days"] = ['required', 'integer', 'min:1', 'max:3650'];
            $rules["policies.{$targetKey}.is_enabled"] = ['nullable', 'boolean'];
            $rules["policies.{$targetKey}.exclude_unread_notifications"] = ['nullable', 'boolean'];
        }

        /** @var array<string, mixed> $validated */
        $validated = $request->validate($rules);

        $payload = [];
        foreach (array_keys($this->retentionPolicyService->targets()) as $targetKey) {
            $targetInput = $validated['policies'][$targetKey] ?? [];
            $payload[$targetKey] = [
                'keep_days' => (int) ($targetInput['keep_days'] ?? 0),
                'archive_grace_days' => (int) ($targetInput['archive_grace_days'] ?? 0),
                'is_enabled' => (bool) ($targetInput['is_enabled'] ?? false),
                'exclude_unread_notifications' => (bool) ($targetInput['exclude_unread_notifications'] ?? false),
            ];
        }

        $this->retentionPolicyService->updatePolicies($payload, auth()->id());

        return redirect()->back()->with('success', 'Data retention policies updated successfully.');
    }

    public function dryRun(Request $request): RedirectResponse
    {
        $target = $this->validateTarget($request);

        $result = $this->dataPurgeService->execute(
            $target === 'all' ? null : [$target],
            true,
            'dry_run',
            auth()->id()
        );

        $summary = $result['summary']['totals'] ?? [];
        $eligible = (int) ($summary['eligible_source_rows'] ?? 0);

        return redirect()->back()->with(
            'success',
            "Dry-run complete. Eligible rows: {$eligible}. No data was deleted."
        );
    }

    public function runNow(Request $request): RedirectResponse
    {
        $target = $this->validateTarget($request);

        $result = $this->dataPurgeService->execute(
            $target === 'all' ? null : [$target],
            false,
            'manual',
            auth()->id()
        );

        $summary = $result['summary']['totals'] ?? [];
        $deleted = (int) ($summary['deleted_source_rows'] ?? 0);
        $archived = (int) ($summary['archived_rows'] ?? 0);

        return redirect()->back()->with(
            'success',
            "Manual purge completed. Archived: {$archived}, deleted source rows: {$deleted}."
        );
    }

    private function validateTarget(Request $request): string
    {
        $allowedTargets = array_keys($this->retentionPolicyService->targets());

        /** @var array<string, mixed> $validated */
        $validated = $request->validate([
            'target' => ['required', Rule::in(array_merge(['all'], $allowedTargets))],
        ]);

        return (string) $validated['target'];
    }
}
