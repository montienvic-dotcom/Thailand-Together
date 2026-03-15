<?php

declare(strict_types=1);

namespace ThailandTogether\Adapters;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;

/**
 * Abstract base class that provides common adapter functionality.
 *
 * Concrete adapters should extend this class and implement the
 * remaining AdapterInterface methods (isAvailable, healthCheck,
 * execute, getSupportedActions).
 */
abstract class BaseAdapter implements AdapterInterface
{
    protected string $name = 'base';
    protected string $version = '1.0.0';

    /** @var array<string, mixed> */
    protected array $config = [];

    /** Maximum number of retries for HTTP calls. */
    protected int $maxRetries = 3;

    /** Milliseconds to wait between retries (doubles each attempt). */
    protected int $retryDelayMs = 200;

    /**
     * @param array<string, mixed> $config Initial configuration
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    // ── AdapterInterface (common implementations) ────────────────────

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function configure(array $config): void
    {
        $this->config = array_merge($this->config, $config);
    }

    // ── HTTP helpers ─────────────────────────────────────────────────

    /**
     * Perform an HTTP GET with automatic retry.
     *
     * @param string               $url
     * @param array<string, mixed> $query
     * @param array<string, string> $headers
     */
    protected function httpGet(string $url, array $query = [], array $headers = []): Response
    {
        return $this->withRetry(function () use ($url, $query, $headers): Response {
            return Http::withHeaders($headers)
                ->timeout($this->config['timeout'] ?? 30)
                ->get($url, $query);
        });
    }

    /**
     * Perform an HTTP POST with automatic retry.
     *
     * @param string               $url
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     */
    protected function httpPost(string $url, array $data = [], array $headers = []): Response
    {
        return $this->withRetry(function () use ($url, $data, $headers): Response {
            return Http::withHeaders($headers)
                ->timeout($this->config['timeout'] ?? 30)
                ->post($url, $data);
        });
    }

    // ── Retry logic ──────────────────────────────────────────────────

    /**
     * Execute a callable with exponential-backoff retry.
     *
     * @template T
     * @param callable(): T $callback
     * @return T
     *
     * @throws \RuntimeException When all retries are exhausted
     */
    protected function withRetry(callable $callback): mixed
    {
        $attempts = 0;
        $lastException = null;

        while ($attempts < $this->maxRetries) {
            try {
                $response = $callback();

                if ($response instanceof Response && $response->serverError()) {
                    throw new \RuntimeException(
                        "Server error {$response->status()} from {$this->name}"
                    );
                }

                return $response;
            } catch (\Exception $e) {
                $lastException = $e;
                $attempts++;

                $this->log('warning', "Attempt {$attempts}/{$this->maxRetries} failed", [
                    'error' => $e->getMessage(),
                ]);

                if ($attempts < $this->maxRetries) {
                    $delay = $this->retryDelayMs * (2 ** ($attempts - 1));
                    usleep($delay * 1000);
                }
            }
        }

        $this->log('error', 'All retry attempts exhausted', [
            'error' => $lastException?->getMessage(),
        ]);

        throw new \RuntimeException(
            "Adapter [{$this->name}] request failed after {$this->maxRetries} attempts: "
            . ($lastException?->getMessage() ?? 'unknown error'),
            0,
            $lastException
        );
    }

    // ── Logging helper ───────────────────────────────────────────────

    /**
     * Log a message under the "adapters" channel.
     *
     * @param string               $level   PSR-3 level (info, warning, error, etc.)
     * @param string               $message
     * @param array<string, mixed> $context
     */
    protected function log(string $level, string $message, array $context = []): void
    {
        $context = array_merge($context, [
            'adapter'  => $this->name,
            'version'  => $this->version,
        ]);

        Log::channel('single')->log($level, "[Adapter:{$this->name}] {$message}", $context);
    }

    // ── Action dispatch helper ───────────────────────────────────────

    /**
     * Map an action string to a method name and call it.
     *
     * Convention: action "listRooms" maps to method "actionListRooms".
     *
     * @param string               $action
     * @param array<string, mixed> $params
     *
     * @throws \InvalidArgumentException When the action is not supported
     */
    protected function dispatch(string $action, array $params = []): mixed
    {
        if (!in_array($action, $this->getSupportedActions(), true)) {
            throw new \InvalidArgumentException(
                "Action [{$action}] is not supported by adapter [{$this->name}]. "
                . 'Supported: ' . implode(', ', $this->getSupportedActions())
            );
        }

        $method = 'action' . ucfirst($action);

        if (!method_exists($this, $method)) {
            throw new \RuntimeException(
                "Action [{$action}] is declared as supported but method [{$method}] does not exist."
            );
        }

        return $this->{$method}($params);
    }
}
