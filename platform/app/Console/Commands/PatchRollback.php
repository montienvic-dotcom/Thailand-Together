<?php

namespace App\Console\Commands;

use App\Services\Patch\PatchManager;
use Illuminate\Console\Command;

class PatchRollback extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'patches:rollback
                            {id : The ID of the patch to rollback}';

    /**
     * The console command description.
     */
    protected $description = 'Rollback a previously applied patch by reversing its changes';

    public function handle(PatchManager $manager): int
    {
        $patchId = $this->argument('id');

        // Show current status before rollback
        $currentStatus = $manager->getPatchStatus($patchId);
        $this->components->info("Rolling back patch: {$patchId}");
        $this->line("  Current status: {$this->formatStatus($currentStatus)}");
        $this->newLine();

        if ($currentStatus === 'not_found') {
            $this->components->error("Patch '{$patchId}' was not found in the registry.");

            return self::FAILURE;
        }

        if (! $this->confirm("Are you sure you want to rollback patch '{$patchId}'?")) {
            $this->components->warn('Rollback cancelled.');

            return self::SUCCESS;
        }

        $result = $manager->rollback($patchId);

        match ($result['status']) {
            'rolled_back' => $this->components->twoColumnDetail(
                "<fg=green>{$result['id']}</>",
                '<fg=green>Rolled back successfully</>'
            ),
            'not_applied' => $this->components->twoColumnDetail(
                "<fg=yellow>{$result['id']}</>",
                '<fg=yellow>Not currently applied</>'
            ),
            'not_found' => $this->components->twoColumnDetail(
                "<fg=red>{$result['id']}</>",
                '<fg=red>Not found in registry</>'
            ),
            'rollback_failed' => $this->components->twoColumnDetail(
                "<fg=red>{$result['id']}</>",
                '<fg=red>Rollback failed</>'
            ),
            default => $this->components->twoColumnDetail(
                $result['id'],
                $result['status']
            ),
        };

        $this->newLine();
        $this->line("  {$result['message']}");

        return $result['status'] === 'rolled_back' ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Format a status string with color for display.
     */
    private function formatStatus(string $status): string
    {
        return match ($status) {
            'applied' => '<fg=green>Applied</>',
            'pending' => '<fg=yellow>Pending</>',
            'conflict' => '<fg=red>Conflict</>',
            'reverted_by_update' => '<fg=red>Reverted by Update</>',
            'code_changed' => '<fg=magenta>Code Changed</>',
            'file_missing' => '<fg=gray>File Missing</>',
            'already_fixed' => '<fg=cyan>Already Fixed</>',
            'not_found' => '<fg=red>Not Found</>',
            'not_applied' => '<fg=yellow>Not Applied</>',
            default => $status,
        };
    }
}
