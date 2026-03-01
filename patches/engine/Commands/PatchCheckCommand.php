<?php

namespace ThailandTogether\Patches\Commands;

use Illuminate\Console\Command;
use ThailandTogether\Patches\PatchManager;

class PatchCheckCommand extends Command
{
    protected $signature = 'patches:check';
    protected $description = 'Check status of all patches (after external source updates)';

    public function handle(): int
    {
        $manager = new PatchManager(base_path('patches'));
        $results = $manager->check();

        if (empty($results)) {
            $this->info('No patches registered.');
            return 0;
        }

        $this->table(
            ['ID', 'App', 'Description', 'Status'],
            array_map(fn ($r) => [
                $r['id'],
                $r['app'],
                $r['description'],
                $this->formatStatus($r['status']),
            ], $results)
        );

        $needsAttention = array_filter($results, fn ($r) => in_array($r['status'], ['reverted_by_update', 'code_changed', 'pending']));
        if (!empty($needsAttention)) {
            $this->warn(count($needsAttention) . ' patch(es) need attention.');
        }

        return 0;
    }

    private function formatStatus(string $status): string
    {
        return match ($status) {
            'applied' => '<fg=green>Applied</>',
            'pending' => '<fg=yellow>Pending (needs applying)</>',
            'reverted_by_update' => '<fg=red>Reverted by update (re-apply needed)</>',
            'code_changed' => '<fg=magenta>Code changed (manual review)</>',
            'file_missing' => '<fg=gray>File missing</>',
            'already_fixed' => '<fg=cyan>Fixed by developer</>',
            'already_applied' => '<fg=green>Already applied</>',
            default => $status,
        };
    }
}
