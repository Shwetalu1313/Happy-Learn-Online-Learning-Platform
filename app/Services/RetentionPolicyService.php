<?php

namespace App\Services;

use App\Models\DataRetentionPolicy;
use Illuminate\Support\Collection;

class RetentionPolicyService
{
    public const TARGET_SYSTEM_ACTIVITIES = 'system_activities';

    public const TARGET_NOTIFICATIONS = 'notifications';

    /**
     * @return array<string, array<string, mixed>>
     */
    public function targets(): array
    {
        return [
            self::TARGET_SYSTEM_ACTIVITIES => [
                'label' => 'System Activities',
                'description' => 'Admin and system activity logs from system_activities table.',
                'default_keep_days' => 180,
                'default_archive_grace_days' => 30,
                'supports_unread_exclusion' => false,
            ],
            self::TARGET_NOTIFICATIONS => [
                'label' => 'Notifications',
                'description' => 'In-app notifications from notifications table.',
                'default_keep_days' => 90,
                'default_archive_grace_days' => 30,
                'supports_unread_exclusion' => true,
            ],
        ];
    }

    public function syncDefaults(): void
    {
        foreach ($this->targets() as $targetKey => $meta) {
            DataRetentionPolicy::query()->firstOrCreate(
                ['target_key' => $targetKey],
                [
                    'keep_days' => (int) $meta['default_keep_days'],
                    'archive_grace_days' => (int) $meta['default_archive_grace_days'],
                    'exclude_unread_notifications' => true,
                    'is_enabled' => true,
                ]
            );
        }
    }

    /**
     * @return Collection<int, DataRetentionPolicy>
     */
    public function all(): Collection
    {
        $this->syncDefaults();

        return DataRetentionPolicy::query()
            ->orderBy('target_key')
            ->get();
    }

    public function findByTarget(string $targetKey): ?DataRetentionPolicy
    {
        $this->syncDefaults();

        return DataRetentionPolicy::query()
            ->where('target_key', $targetKey)
            ->first();
    }

    /**
     * @param  array<string, array<string, mixed>>  $policies
     */
    public function updatePolicies(array $policies, ?int $updatedBy): void
    {
        $targets = $this->targets();
        foreach ($targets as $targetKey => $meta) {
            $payload = $policies[$targetKey] ?? [];

            DataRetentionPolicy::query()->updateOrCreate(
                ['target_key' => $targetKey],
                [
                    'keep_days' => max(1, (int) ($payload['keep_days'] ?? $meta['default_keep_days'])),
                    'archive_grace_days' => max(1, (int) ($payload['archive_grace_days'] ?? $meta['default_archive_grace_days'])),
                    'exclude_unread_notifications' => (bool) ($payload['exclude_unread_notifications'] ?? true),
                    'is_enabled' => (bool) ($payload['is_enabled'] ?? false),
                    'updated_by' => $updatedBy,
                ]
            );
        }
    }

    /**
     * @param  array<int, string>|null  $targets
     * @return array<int, string>
     */
    public function normalizeTargets(?array $targets): array
    {
        $allTargets = array_keys($this->targets());
        if (empty($targets)) {
            return $allTargets;
        }

        return array_values(array_intersect($allTargets, $targets));
    }
}
