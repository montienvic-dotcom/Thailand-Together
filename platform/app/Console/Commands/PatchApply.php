<?php

namespace App\Console\Commands;

use App\Services\Patch\PatchManager;
use Illuminate\Console\Command;

class PatchApply extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'patches:apply
                            {--id= : Apply a specific patch by its ID}
                            {--all : Apply all pending patches}';

    /**
     * The console command description.
     */
    protected $description = 'Apply pending patches to external scripts and third-party code';

    public function handle(PatchManager $manager): int
    {
        $patchId = $this->option('id');
        $applyAll = $this->option('all');

        if (! $patchId && ! $applyAll) {
            $this->error('Please specify --id=<patch-id> or --all to apply patches.');
            $this->newLine();
            $this->line('  <comment>php artisan patches:apply --id=patch-001</comment>  Apply a specific patch');
            $this->line('  <comment>php artisan patches:apply --all</comment>           Apply all pending patches');

            return self::FAILURE;
        }

        if ($patchId) {
            return $this->applySingle($manager, $patchId);
        }

        return $this->applyAllPatches($manager);
    }

    /**
     * Apply a single patch by ID.
     */
    private function applySingle(PatchManager $manager, string $patchId): int
    {
        $this->components->info("Applying patch: {$patchId}");

        $result = $manager->apply($patchId);

        match ($result['status']) {
            'applied' => $this->components->twoColumnDetail(
                "<fg=green>{$result['id']}</>",
                "<fg=green>Applied</>"
            ),
            'already_applied' => $this->components->twoColumnDetail(
                "<fg=yellow>{$result['id']}</>",
                "<fg=yellow>Already applied</>"
            ),
            'not_found' => $this->components->twoColumnDetail(
                "<fg=red>{$result['id']}</>",
                "<fg=red>Not found in registry</>"
            ),
            'failed' => $this->components->twoColumnDetail(
                "<fg=red>{$result['id']}</>",
                "<fg=red>Failed</>"
            ),
            default => $this->components->twoColumnDetail(
                $result['id'],
                $result['status']
            ),
        };

        $this->newLine();
        $this->line("  {$result['message']}");

        return $result['status'] === 'applied' ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Apply all pending patches.
     */
    private function applyAllPatches(PatchManager $manager): int
    {
        $this->components->info('Applying all pending patches...');
        $this->newLine();

        $results = $manager->applyAll();

        if (empty($results)) {
            $this->components->warn('No patches found in registry.');

            return self::SUCCESS;
        }

        $applied = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($results as $result) {
            $icon = match ($result['status']) {
                'applied' => '<fg=green>APPLIED</>',
                'skipped' => '<fg=blue>SKIPPED</>',
                'already_applied' => '<fg=yellow>SKIP</>',
                'failed' => '<fg=red>FAILED</>',
                default => "<fg=gray>{$result['status']}</>",
            };

            $this->components->twoColumnDetail($result['id'], $icon);
            $this->line("    <fg=gray>{$result['message']}</>");

            match ($result['status']) {
                'applied' => $applied++,
                'failed' => $failed++,
                default => $skipped++,
            };
        }

        $this->newLine();
        $this->components->info(
            "Done: {$applied} applied, {$skipped} skipped, {$failed} failed."
        );

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
