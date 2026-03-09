<?php

namespace App\Console\Commands;

use App\Services\Merchant\MerchantImportService;
use Illuminate\Console\Command;

class MerchantImportCommand extends Command
{
    protected $signature = 'merchant:import
                            {file : Path to CSV file}
                            {--batch-code= : Custom batch code (auto-generated if not provided)}';

    protected $description = 'Import merchants from CSV into the system (staging → validate → upsert)';

    public function handle(MerchantImportService $importService): int
    {
        $filePath = $this->argument('file');
        $batchCode = $this->option('batch-code') ?? 'IMPORT_' . date('Ymd_His');

        if (! file_exists($filePath)) {
            $this->error("File not found: {$filePath}");

            return self::FAILURE;
        }

        $this->info("Starting import: {$filePath}");
        $this->info("Batch code: {$batchCode}");
        $this->newLine();

        try {
            $result = $importService->importFromCsv($filePath, $batchCode);

            $this->table(
                ['Metric', 'Count'],
                [
                    ['Total rows', $result['total']],
                    ['Imported', $result['imported']],
                    ['Errors', $result['errors']],
                    ['Skipped', $result['skipped']],
                ]
            );

            if ($result['errors'] > 0) {
                $this->warn("Check stg_merchant_import for error details (batch_code={$batchCode})");
            }

            $this->info('Import completed successfully.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Import failed: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
