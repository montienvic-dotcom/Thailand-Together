<?php

namespace App\Services\ApiGateway\Adapters;

/**
 * Data Exchange adapter for import/export/sync operations.
 * Used for merchant data, tourist data, and cross-cluster sync.
 */
class DataExchangeAdapter extends BaseAdapter
{
    public function providerName(): string
    {
        return $this->config['provider_name'] ?? 'Data Exchange';
    }

    /**
     * Import data from external source.
     */
    public function import(string $dataType, array $records, array $options = []): mixed
    {
        return $this->execute('POST', '/import', array_merge([
            'data_type' => $dataType,
            'records' => $records,
        ], $options));
    }

    /**
     * Export data to external destination.
     */
    public function export(string $dataType, array $filters = [], string $format = 'json'): mixed
    {
        return $this->execute('POST', '/export', [
            'data_type' => $dataType,
            'filters' => $filters,
            'format' => $format,
        ]);
    }

    /**
     * Sync data between clusters or external systems.
     */
    public function sync(string $sourceId, string $targetId, string $dataType, array $options = []): mixed
    {
        return $this->execute('POST', '/sync', array_merge([
            'source_id' => $sourceId,
            'target_id' => $targetId,
            'data_type' => $dataType,
        ], $options));
    }

    /**
     * Check sync status for a job.
     */
    public function syncStatus(string $jobId): mixed
    {
        return $this->execute('GET', "/sync/status/{$jobId}");
    }

    protected function baseUrl(): string
    {
        return $this->credential?->credential('base_url')
            ?? $this->config['base_url']
            ?? '';
    }

    protected function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . ($this->credential?->credential('api_key') ?? ''),
            'Content-Type' => 'application/json',
        ];
    }
}
