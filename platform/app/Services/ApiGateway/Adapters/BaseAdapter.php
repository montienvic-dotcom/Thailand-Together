<?php

namespace App\Services\ApiGateway\Adapters;

use App\Models\Integration\ApiCredential;
use App\Services\ApiGateway\Contracts\ApiAdapterInterface;
use Illuminate\Support\Facades\Http;

/**
 * Base adapter with common HTTP functionality.
 * Extend this for specific providers.
 */
abstract class BaseAdapter implements ApiAdapterInterface
{
    protected ?ApiCredential $credential;
    protected array $config;

    public function __construct(?ApiCredential $credential, array $config = [])
    {
        $this->credential = $credential;
        $this->config = $config;
    }

    public function execute(string $method, string $endpoint, array $data = []): mixed
    {
        $url = rtrim($this->baseUrl(), '/') . '/' . ltrim($endpoint, '/');
        $headers = $this->headers();

        $response = Http::withHeaders($headers)
            ->timeout($this->config['timeout'] ?? 30)
            ->{strtolower($method)}($url, $data);

        if ($response->failed()) {
            throw new \RuntimeException(
                "{$this->providerName()} API error: {$response->status()} - {$response->body()}"
            );
        }

        return $response->json();
    }

    public function healthCheck(): bool
    {
        try {
            $this->execute('GET', $this->healthEndpoint());
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Base URL for this provider's API.
     */
    abstract protected function baseUrl(): string;

    /**
     * Default headers including auth.
     */
    abstract protected function headers(): array;

    /**
     * Endpoint to check health/connectivity.
     */
    protected function healthEndpoint(): string
    {
        return '/';
    }
}
