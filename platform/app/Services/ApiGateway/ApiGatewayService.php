<?php

namespace App\Services\ApiGateway;

use App\Models\Integration\ApiProvider;
use App\Services\ApiGateway\Contracts\ApiAdapterInterface;
use App\Services\Cluster\ClusterManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Central API Gateway service.
 * Routes API calls through the correct adapter with the right credentials.
 * All third-party integrations go through this gateway.
 */
class ApiGatewayService
{
    private array $adapters = [];

    public function __construct(
        private ClusterManager $clusterManager,
    ) {}

    /**
     * Get an adapter for a specific API provider.
     */
    public function adapter(string $providerSlug): ApiAdapterInterface
    {
        if (isset($this->adapters[$providerSlug])) {
            return $this->adapters[$providerSlug];
        }

        $provider = ApiProvider::where('slug', $providerSlug)->active()->firstOrFail();

        $adapterClass = $provider->adapter_class;
        if (!$adapterClass || !class_exists($adapterClass)) {
            throw new \RuntimeException("Adapter class not found for provider: {$providerSlug}");
        }

        $credential = $provider->credentialsFor(
            $this->clusterManager->currentId(),
            $this->clusterManager->countryId(),
            app()->environment('production') ? 'production' : 'sandbox'
        );

        $adapter = new $adapterClass($credential, $provider->default_config ?? []);
        $this->adapters[$providerSlug] = $adapter;

        return $adapter;
    }

    /**
     * Execute an API call and log it.
     */
    public function call(string $providerSlug, string $method, string $endpoint, array $data = []): mixed
    {
        $startTime = microtime(true);
        $provider = ApiProvider::where('slug', $providerSlug)->firstOrFail();

        try {
            $adapter = $this->adapter($providerSlug);
            $result = $adapter->execute($method, $endpoint, $data);

            $this->logCall($provider->id, $method, $endpoint, 200, $startTime);

            return $result;
        } catch (\Throwable $e) {
            $this->logCall($provider->id, $method, $endpoint, 500, $startTime, $e->getMessage());

            Log::error("API Gateway error [{$providerSlug}]: {$e->getMessage()}", [
                'provider' => $providerSlug,
                'method' => $method,
                'endpoint' => $endpoint,
            ]);

            throw $e;
        }
    }

    /**
     * Get all active providers by category.
     */
    public function providersByCategory(string $category): \Illuminate\Database\Eloquent\Collection
    {
        return ApiProvider::active()
            ->category($category)
            ->get();
    }

    private function logCall(int $providerId, string $method, string $endpoint, int $statusCode, float $startTime, ?string $error = null): void
    {
        $responseTime = (int) ((microtime(true) - $startTime) * 1000);

        DB::table('api_logs')->insert([
            'api_provider_id' => $providerId,
            'cluster_id' => $this->clusterManager->currentId(),
            'method' => strtoupper($method),
            'endpoint' => $endpoint,
            'status_code' => $statusCode,
            'response_time_ms' => $responseTime,
            'error_message' => $error,
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);
    }
}
