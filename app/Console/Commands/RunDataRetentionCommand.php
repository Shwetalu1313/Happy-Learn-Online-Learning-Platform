<?php

namespace App\Console\Commands;

use App\Services\DataPurgeService;
use Illuminate\Console\Command;
use Throwable;

class RunDataRetentionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retention:run
                            {--dry-run : Preview eligible rows without deleting}
                            {--target=* : Target keys (system_activities, notifications)}
                            {--trigger=scheduled : Run type label (scheduled|manual)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute data retention purge policy for configured targets.';

    public function __construct(private readonly DataPurgeService $dataPurgeService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $targets = $this->option('target');
        $trigger = $dryRun ? 'dry_run' : (string) $this->option('trigger');
        $runType = in_array($trigger, ['scheduled', 'manual', 'dry_run'], true) ? $trigger : 'scheduled';

        try {
            $result = $this->dataPurgeService->execute(
                is_array($targets) && ! empty($targets) ? $targets : null,
                $dryRun,
                $runType
            );

            $run = $result['run'];
            $summary = $result['summary'];

            $this->info("Retention run #{$run->id} finished with status: {$run->status}");
            $this->line(json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '{}');

            return self::SUCCESS;
        } catch (Throwable $throwable) {
            report($throwable);
            $this->error('Retention run failed: '.$throwable->getMessage());

            return self::FAILURE;
        }
    }
}
