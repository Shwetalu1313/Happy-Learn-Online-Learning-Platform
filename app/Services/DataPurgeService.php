<?php

namespace App\Services;

use App\Models\DataPurgeArchive;
use App\Models\DataPurgeRun;
use Illuminate\Support\Facades\DB;
use Throwable;

class DataPurgeService
{
    public function __construct(private readonly RetentionPolicyService $retentionPolicyService) {}

    /**
     * @param  array<int, string>|null  $targets
     * @return array{run: DataPurgeRun, summary: array<string, mixed>}
     *
     * @throws Throwable
     */
    public function execute(?array $targets, bool $dryRun, string $runType, ?int $triggeredBy = null): array
    {
        $this->retentionPolicyService->syncDefaults();
        $targetKeys = $this->retentionPolicyService->normalizeTargets($targets);

        $run = DataPurgeRun::query()->create([
            'run_type' => $runType,
            'status' => 'started',
            'triggered_by' => $triggeredBy,
            'started_at' => now(),
        ]);

        $summary = [
            'dry_run' => $dryRun,
            'targets' => [],
            'totals' => [
                'eligible_source_rows' => 0,
                'archived_rows' => 0,
                'deleted_source_rows' => 0,
                'expired_archive_rows' => 0,
                'deleted_archive_rows' => 0,
            ],
        ];

        try {
            foreach ($targetKeys as $targetKey) {
                $policy = $this->retentionPolicyService->findByTarget($targetKey);
                if ($policy === null) {
                    continue;
                }

                $targetSummary = match ($targetKey) {
                    RetentionPolicyService::TARGET_SYSTEM_ACTIVITIES => $this->processSystemActivities($policy->toArray(), $dryRun),
                    RetentionPolicyService::TARGET_NOTIFICATIONS => $this->processNotifications($policy->toArray(), $dryRun),
                    default => $this->emptySummary($policy->toArray()),
                };

                $summary['targets'][$targetKey] = $targetSummary;
                $summary['totals']['eligible_source_rows'] += (int) ($targetSummary['eligible_source_rows'] ?? 0);
                $summary['totals']['archived_rows'] += (int) ($targetSummary['archived_rows'] ?? 0);
                $summary['totals']['deleted_source_rows'] += (int) ($targetSummary['deleted_source_rows'] ?? 0);
                $summary['totals']['expired_archive_rows'] += (int) ($targetSummary['expired_archive_rows'] ?? 0);
                $summary['totals']['deleted_archive_rows'] += (int) ($targetSummary['deleted_archive_rows'] ?? 0);
            }

            $run->update([
                'status' => 'completed',
                'summary' => $summary,
                'finished_at' => now(),
            ]);
        } catch (Throwable $throwable) {
            $run->update([
                'status' => 'failed',
                'summary' => $summary,
                'error_message' => $throwable->getMessage(),
                'finished_at' => now(),
            ]);

            throw $throwable;
        }

        return [
            'run' => $run->fresh(),
            'summary' => $summary,
        ];
    }

    /**
     * @param  array<string, mixed>  $policy
     * @return array<string, mixed>
     */
    private function processSystemActivities(array $policy, bool $dryRun): array
    {
        if (! ($policy['is_enabled'] ?? false)) {
            return $this->emptySummary($policy);
        }

        $keepDays = (int) $policy['keep_days'];
        $graceDays = (int) $policy['archive_grace_days'];
        $cutoff = now()->subDays($keepDays);

        $baseQuery = DB::table('system_activities')->where('created_at', '<', $cutoff);
        $eligibleCount = (int) (clone $baseQuery)->count();

        $result = [
            'enabled' => true,
            'keep_days' => $keepDays,
            'archive_grace_days' => $graceDays,
            'eligible_source_rows' => $eligibleCount,
            'archived_rows' => 0,
            'deleted_source_rows' => 0,
            'expired_archive_rows' => (int) DataPurgeArchive::query()
                ->where('target_key', RetentionPolicyService::TARGET_SYSTEM_ACTIVITIES)
                ->where('purge_after_at', '<', now())
                ->count(),
            'deleted_archive_rows' => 0,
        ];

        if ($dryRun) {
            return $result;
        }

        $baseQuery
            ->orderBy('id')
            ->chunkById(500, function ($rows) use (&$result, $graceDays): void {
                $archiveRows = [];
                $ids = [];

                foreach ($rows as $row) {
                    $ids[] = $row->id;
                    $archiveAt = now();
                    $payload = json_encode((array) $row, JSON_UNESCAPED_UNICODE) ?: '{}';

                    $archiveRows[] = [
                        'target_key' => RetentionPolicyService::TARGET_SYSTEM_ACTIVITIES,
                        'source_pk' => (string) $row->id,
                        'payload' => $payload,
                        'source_created_at' => $row->created_at,
                        'archived_at' => $archiveAt,
                        'purge_after_at' => $archiveAt->copy()->addDays($graceDays),
                        'created_at' => $archiveAt,
                        'updated_at' => $archiveAt,
                    ];
                }

                if (! empty($archiveRows)) {
                    DataPurgeArchive::query()->upsert(
                        $archiveRows,
                        ['target_key', 'source_pk'],
                        ['payload', 'source_created_at', 'archived_at', 'purge_after_at', 'updated_at']
                    );
                }

                $result['archived_rows'] += count($archiveRows);
                $result['deleted_source_rows'] += DB::table('system_activities')->whereIn('id', $ids)->delete();
            }, 'id');

        $result['deleted_archive_rows'] = DataPurgeArchive::query()
            ->where('target_key', RetentionPolicyService::TARGET_SYSTEM_ACTIVITIES)
            ->where('purge_after_at', '<', now())
            ->delete();

        return $result;
    }

    /**
     * @param  array<string, mixed>  $policy
     * @return array<string, mixed>
     */
    private function processNotifications(array $policy, bool $dryRun): array
    {
        if (! ($policy['is_enabled'] ?? false)) {
            return $this->emptySummary($policy);
        }

        $keepDays = (int) $policy['keep_days'];
        $graceDays = (int) $policy['archive_grace_days'];
        $excludeUnread = (bool) ($policy['exclude_unread_notifications'] ?? true);
        $cutoff = now()->subDays($keepDays);

        $baseQuery = DB::table('notifications')->where('created_at', '<', $cutoff);
        if ($excludeUnread) {
            $baseQuery->whereNotNull('read_at');
        }

        $eligibleCount = (int) (clone $baseQuery)->count();
        $result = [
            'enabled' => true,
            'keep_days' => $keepDays,
            'archive_grace_days' => $graceDays,
            'exclude_unread_notifications' => $excludeUnread,
            'eligible_source_rows' => $eligibleCount,
            'archived_rows' => 0,
            'deleted_source_rows' => 0,
            'expired_archive_rows' => (int) DataPurgeArchive::query()
                ->where('target_key', RetentionPolicyService::TARGET_NOTIFICATIONS)
                ->where('purge_after_at', '<', now())
                ->count(),
            'deleted_archive_rows' => 0,
        ];

        if ($dryRun) {
            return $result;
        }

        while (true) {
            $ids = (clone $baseQuery)
                ->orderBy('created_at')
                ->limit(500)
                ->pluck('id')
                ->all();

            if (empty($ids)) {
                break;
            }

            $rows = DB::table('notifications')->whereIn('id', $ids)->get();
            $archiveRows = [];

            foreach ($rows as $row) {
                $archiveAt = now();
                $payload = json_encode((array) $row, JSON_UNESCAPED_UNICODE) ?: '{}';

                $archiveRows[] = [
                    'target_key' => RetentionPolicyService::TARGET_NOTIFICATIONS,
                    'source_pk' => (string) $row->id,
                    'payload' => $payload,
                    'source_created_at' => $row->created_at,
                    'archived_at' => $archiveAt,
                    'purge_after_at' => $archiveAt->copy()->addDays($graceDays),
                    'created_at' => $archiveAt,
                    'updated_at' => $archiveAt,
                ];
            }

            if (! empty($archiveRows)) {
                DataPurgeArchive::query()->upsert(
                    $archiveRows,
                    ['target_key', 'source_pk'],
                    ['payload', 'source_created_at', 'archived_at', 'purge_after_at', 'updated_at']
                );
            }

            $result['archived_rows'] += count($archiveRows);
            $deleted = DB::table('notifications')->whereIn('id', $ids)->delete();
            $result['deleted_source_rows'] += $deleted;

            if ($deleted === 0) {
                break;
            }
        }

        $result['deleted_archive_rows'] = DataPurgeArchive::query()
            ->where('target_key', RetentionPolicyService::TARGET_NOTIFICATIONS)
            ->where('purge_after_at', '<', now())
            ->delete();

        return $result;
    }

    /**
     * @param  array<string, mixed>  $policy
     * @return array<string, mixed>
     */
    private function emptySummary(array $policy): array
    {
        return [
            'enabled' => false,
            'keep_days' => (int) ($policy['keep_days'] ?? 0),
            'archive_grace_days' => (int) ($policy['archive_grace_days'] ?? 0),
            'exclude_unread_notifications' => (bool) ($policy['exclude_unread_notifications'] ?? true),
            'eligible_source_rows' => 0,
            'archived_rows' => 0,
            'deleted_source_rows' => 0,
            'expired_archive_rows' => 0,
            'deleted_archive_rows' => 0,
        ];
    }
}
