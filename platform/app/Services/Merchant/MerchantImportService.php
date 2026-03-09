<?php

namespace App\Services\Merchant;

use Illuminate\Support\Facades\DB;

class MerchantImportService
{
    /**
     * Import merchants from a CSV file into staging, validate, then upsert.
     *
     * @return array{imported: int, errors: int, skipped: int, batch_code: string}
     */
    public function importFromCsv(string $filePath, string $batchCode): array
    {
        // 1. Create batch record
        DB::table('merchant_import_batch')->insert([
            'batch_code' => $batchCode,
            'batch_label' => basename($filePath),
            'status' => 'PROCESSING',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Load CSV into staging
        $totalRows = $this->loadCsvToStaging($filePath, $batchCode);

        // 3. Validate staging rows
        $errorCount = $this->validateStagingRows($batchCode);

        // 4. Upsert valid rows
        $importedCount = $this->upsertFromStaging($batchCode);

        // 5. Refresh place_merchant ranking
        $this->refreshPlaceMerchantRanking();

        // 6. Update batch status
        $skipped = $totalRows - $importedCount - $errorCount;
        DB::table('merchant_import_batch')
            ->where('batch_code', $batchCode)
            ->update([
                'status' => 'COMPLETED',
                'total_rows' => $totalRows,
                'imported_rows' => $importedCount,
                'error_rows' => $errorCount,
                'updated_at' => now(),
            ]);

        return [
            'batch_code' => $batchCode,
            'imported' => $importedCount,
            'errors' => $errorCount,
            'skipped' => $skipped,
            'total' => $totalRows,
        ];
    }

    /**
     * Load CSV rows into stg_merchant_import.
     */
    public function loadCsvToStaging(string $filePath, string $batchCode): int
    {
        $handle = fopen($filePath, 'r');
        if (! $handle) {
            throw new \RuntimeException("Cannot open file: {$filePath}");
        }

        $headers = fgetcsv($handle);
        if (! $headers) {
            throw new \RuntimeException('CSV file is empty or has no headers');
        }

        // Normalize headers
        $headers = array_map(fn ($h) => strtolower(trim($h)), $headers);

        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) {
                continue;
            }

            $data = array_combine($headers, $row);

            DB::table('stg_merchant_import')->insert([
                'batch_code' => $batchCode,
                'merchant_code' => $data['merchant_code'] ?? '',
                'merchant_name_th' => $data['merchant_name_th'] ?? '',
                'merchant_name_en' => $data['merchant_name_en'] ?? null,
                'merchant_desc_th' => $data['merchant_desc_th'] ?? null,
                'merchant_desc_en' => $data['merchant_desc_en'] ?? null,
                'default_tier_code' => $data['default_tier_code'] ?? 'S',
                'is_active' => (int) ($data['is_active'] ?? 1),
                'phone' => $data['phone'] ?? null,
                'website' => $data['website'] ?? null,
                'price_level' => (int) ($data['price_level'] ?? 2),
                'lat' => $data['lat'] ?: null,
                'lng' => $data['lng'] ?: null,
                'place_code' => $data['place_code'] ?? null,
                'is_primary_hint' => (int) ($data['is_primary_hint'] ?? 0),
                'onsite_note' => $data['onsite_note'] ?? null,
                'open_hours' => $data['open_hours'] ?? null,
                'service_tags' => $data['service_tags'] ?? null,
                'source_ref' => $data['source_ref'] ?? null,
                'validation_status' => 'PENDING',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $count++;
        }

        fclose($handle);

        return $count;
    }

    /**
     * Validate staging rows: check for duplicates, missing fields, invalid place_codes.
     */
    public function validateStagingRows(string $batchCode): int
    {
        $errorCount = 0;

        // Mark rows with empty merchant_code
        $errorCount += DB::table('stg_merchant_import')
            ->where('batch_code', $batchCode)
            ->where('validation_status', 'PENDING')
            ->where(function ($q) {
                $q->whereNull('merchant_code')->orWhere('merchant_code', '');
            })
            ->update([
                'validation_status' => 'ERROR',
                'validation_errors' => 'merchant_code is required',
                'updated_at' => now(),
            ]);

        // Mark rows with empty merchant_name_th
        $errorCount += DB::table('stg_merchant_import')
            ->where('batch_code', $batchCode)
            ->where('validation_status', 'PENDING')
            ->where(function ($q) {
                $q->whereNull('merchant_name_th')->orWhere('merchant_name_th', '');
            })
            ->update([
                'validation_status' => 'ERROR',
                'validation_errors' => 'merchant_name_th is required',
                'updated_at' => now(),
            ]);

        // Mark rows with invalid place_code (not in place table)
        $errorCount += DB::table('stg_merchant_import')
            ->where('batch_code', $batchCode)
            ->where('validation_status', 'PENDING')
            ->whereNotNull('place_code')
            ->where('place_code', '!=', '')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('place')
                    ->whereColumn('place.place_code', 'stg_merchant_import.place_code');
            })
            ->update([
                'validation_status' => 'ERROR',
                'validation_errors' => 'place_code not found in place table',
                'updated_at' => now(),
            ]);

        // Mark remaining as VALID
        DB::table('stg_merchant_import')
            ->where('batch_code', $batchCode)
            ->where('validation_status', 'PENDING')
            ->update([
                'validation_status' => 'VALID',
                'updated_at' => now(),
            ]);

        return $errorCount;
    }

    /**
     * Upsert valid staging rows into merchant, merchant_i18n, place_merchant.
     */
    public function upsertFromStaging(string $batchCode): int
    {
        $validRows = DB::table('stg_merchant_import')
            ->where('batch_code', $batchCode)
            ->where('validation_status', 'VALID')
            ->get();

        $imported = 0;
        $pattayaClusterId = DB::table('clusters')->where('code', 'PTY')->value('id');

        foreach ($validRows as $row) {
            // Upsert merchant
            $merchantId = DB::table('merchant')
                ->where('merchant_code', $row->merchant_code)
                ->value('merchant_id');

            if ($merchantId) {
                DB::table('merchant')->where('merchant_id', $merchantId)->update([
                    'merchant_name_th' => $row->merchant_name_th,
                    'merchant_name_en' => $row->merchant_name_en ?: $row->merchant_name_th,
                    'merchant_desc_th' => $row->merchant_desc_th,
                    'merchant_desc_en' => $row->merchant_desc_en,
                    'default_tier_code' => $row->default_tier_code,
                    'is_active' => $row->is_active,
                    'phone' => $row->phone,
                    'website' => $row->website,
                    'price_level' => $row->price_level,
                    'lat' => $row->lat,
                    'lng' => $row->lng,
                    'open_hours' => $row->open_hours,
                    'service_tags' => $row->service_tags,
                    'onsite_note' => $row->onsite_note,
                    'source_ref' => $row->source_ref,
                    'updated_at' => now(),
                ]);
            } else {
                $merchantId = DB::table('merchant')->insertGetId([
                    'merchant_code' => $row->merchant_code,
                    'merchant_name_th' => $row->merchant_name_th,
                    'merchant_name_en' => $row->merchant_name_en ?: $row->merchant_name_th,
                    'merchant_desc_th' => $row->merchant_desc_th,
                    'merchant_desc_en' => $row->merchant_desc_en,
                    'default_tier_code' => $row->default_tier_code,
                    'is_active' => $row->is_active,
                    'phone' => $row->phone,
                    'website' => $row->website,
                    'price_level' => $row->price_level,
                    'lat' => $row->lat,
                    'lng' => $row->lng,
                    'open_hours' => $row->open_hours,
                    'service_tags' => $row->service_tags,
                    'onsite_note' => $row->onsite_note,
                    'source_ref' => $row->source_ref,
                    'cluster_id' => $pattayaClusterId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Upsert i18n
            foreach (['th', 'en'] as $lang) {
                $name = $lang === 'th' ? $row->merchant_name_th : ($row->merchant_name_en ?: $row->merchant_name_th);
                $desc = $lang === 'th' ? $row->merchant_desc_th : $row->merchant_desc_en;

                DB::table('merchant_i18n')->updateOrInsert(
                    ['merchant_id' => $merchantId, 'lang' => $lang],
                    ['name' => $name, 'description' => $desc, 'updated_at' => now()]
                );
            }

            // Link to place if place_code provided
            if ($row->place_code) {
                $placeId = DB::table('place')->where('place_code', $row->place_code)->value('place_id');
                if ($placeId) {
                    DB::table('place_merchant')->updateOrInsert(
                        ['place_id' => $placeId, 'merchant_id' => $merchantId],
                        [
                            'is_primary' => $row->is_primary_hint,
                            'updated_at' => now(),
                        ]
                    );
                }
            }

            $imported++;
        }

        return $imported;
    }

    /**
     * Recompute is_primary and sort_order for all place_merchant rows.
     * Primary = rank 1 per place, ordered by tier (XL>E>M>S).
     */
    public function refreshPlaceMerchantRanking(): void
    {
        // Use a raw query for the ranking update (same as spec)
        DB::statement("
            CREATE TEMPORARY TABLE IF NOT EXISTS tmp_pm_rank AS
            SELECT
                pm.place_id,
                pm.merchant_id,
                ROW_NUMBER() OVER (
                    PARTITION BY pm.place_id
                    ORDER BY
                        pm.is_primary DESC,
                        CASE m.default_tier_code
                            WHEN 'XL' THEN 4 WHEN 'E' THEN 3 WHEN 'M' THEN 2 WHEN 'S' THEN 1 ELSE 0
                        END DESC,
                        pm.merchant_id ASC
                ) AS rn
            FROM place_merchant pm
            JOIN merchant m ON m.merchant_id = pm.merchant_id
        ");

        DB::statement("
            UPDATE place_merchant pm
            JOIN tmp_pm_rank r ON r.place_id = pm.place_id AND r.merchant_id = pm.merchant_id
            SET pm.is_primary = CASE WHEN r.rn = 1 THEN 1 ELSE 0 END,
                pm.sort_order = r.rn
        ");

        DB::statement('DROP TEMPORARY TABLE IF EXISTS tmp_pm_rank');
    }
}
