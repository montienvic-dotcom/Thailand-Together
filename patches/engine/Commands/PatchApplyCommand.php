<?php

namespace ThailandTogether\Patches\Commands;

use Illuminate\Console\Command;
use ThailandTogether\Patches\PatchManager;

class PatchApplyCommand extends Command
{
    protected $signature = 'patches:apply {--id= : Apply a specific patch by ID}';
    protected $description = 'Apply pending patches to external scripts';

    public function handle(): int
    {
        $manager = new PatchManager(base_path('patches'));

        $this->info('Applying patches...');

        $results = $manager->applyAll();

        foreach ($results as $result) {
            match ($result['status']) {
                'applied' => $this->info("  [OK] {$result['id']}"),
                'already_applied' => $this->line("  [SKIP] {$result['id']} (already applied)"),
                'failed' => $this->error("  [FAIL] {$result['id']}"),
                default => $this->line("  [?] {$result['id']}: {$result['status']}"),
            };
        }

        $applied = count(array_filter($results, fn ($r) => $r['status'] === 'applied'));
        $this->newLine();
        $this->info("{$applied} patch(es) applied successfully.");

        return 0;
    }
}
