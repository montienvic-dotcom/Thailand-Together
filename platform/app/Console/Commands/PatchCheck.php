<?php

namespace App\Console\Commands;

use App\Services\Patch\PatchManager;
use Illuminate\Console\Command;

class PatchCheck extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'patches:check';

    /**
     * The console command description.
     */
    protected $description = 'Check the status of all registered patches';

    public function handle(PatchManager $manager): int
    {
        $this->components->info('Checking patch status...');
        $this->newLine();

        $results = $manager->check();

        if (empty($results)) {
            $this->components->info('No patches registered in the registry.');
            $this->newLine();
            $this->line('  Registry location: <comment>' . base_path('../patches/registry.json') . '</comment>');

            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'App', 'Type', 'Description', 'Status'],
            array_map(fn (array $r) => [
                $r['id'],
                $r['app'],
                $r['type'],
                $this->truncate($r['description'], 40),
                $this->formatStatus($r['status']),
            ], $results)
        );

        $this->newLine();
        $this->renderSummary($results);

        return self::SUCCESS;
    }

    /**
     * Format a status string with color.
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
            default => $status,
        };
    }

    /**
     * Render a summary of patch statuses.
     */
    private function renderSummary(array $results): void
    {
        $counts = [];
        foreach ($results as $r) {
            $status = $r['status'];
            $counts[$status] = ($counts[$status] ?? 0) + 1;
        }

        $total = count($results);
        $this->line("  <fg=white>Total patches:</> {$total}");

        if (isset($counts['applied'])) {
            $this->line("  <fg=green>Applied:</>       {$counts['applied']}");
        }
        if (isset($counts['pending'])) {
            $this->line("  <fg=yellow>Pending:</>       {$counts['pending']}");
        }
        if (isset($counts['conflict'])) {
            $this->line("  <fg=red>Conflicts:</>     {$counts['conflict']}");
        }
        if (isset($counts['reverted_by_update'])) {
            $this->line("  <fg=red>Reverted:</>      {$counts['reverted_by_update']}");
        }
        if (isset($counts['code_changed'])) {
            $this->line("  <fg=magenta>Changed:</>       {$counts['code_changed']}");
        }
        if (isset($counts['file_missing'])) {
            $this->line("  <fg=gray>File missing:</>  {$counts['file_missing']}");
        }
        if (isset($counts['already_fixed'])) {
            $this->line("  <fg=cyan>Already fixed:</> {$counts['already_fixed']}");
        }

        $needsAttention = ($counts['pending'] ?? 0)
            + ($counts['conflict'] ?? 0)
            + ($counts['reverted_by_update'] ?? 0)
            + ($counts['code_changed'] ?? 0);

        $this->newLine();
        if ($needsAttention > 0) {
            $this->components->warn("{$needsAttention} patch(es) need attention.");
        } else {
            $this->components->info('All patches are in good standing.');
        }
    }

    /**
     * Truncate a string to a given length.
     */
    private function truncate(string $value, int $length): string
    {
        if (mb_strlen($value) <= $length) {
            return $value;
        }

        return mb_substr($value, 0, $length - 3) . '...';
    }
}
