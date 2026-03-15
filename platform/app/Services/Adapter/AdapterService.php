<?php

declare(strict_types=1);

namespace App\Services\Adapter;

use ThailandTogether\Adapters\AdapterInterface;
use ThailandTogether\Adapters\AdapterRegistry;

/**
 * Laravel service that bridges the external adapters/ directory
 * with the platform application.
 *
 * Registered as a singleton in the service container so that adapter
 * discovery only happens once per request lifecycle.
 */
class AdapterService
{
    private AdapterRegistry $registry;
    private bool $discovered = false;

    public function __construct()
    {
        $this->registry = new AdapterRegistry();
    }

    /**
     * Ensure adapters have been discovered from disk.
     */
    private function ensureDiscovered(): void
    {
        if ($this->discovered) {
            return;
        }

        $adaptersPath = $this->getAdaptersPath();

        // Require the core adapter files first.
        $this->requireCoreFiles($adaptersPath);

        $this->registry->discover($adaptersPath);
        $this->discovered = true;
    }

    /**
     * Require the interface and base class before discovery.
     */
    private function requireCoreFiles(string $basePath): void
    {
        $coreFiles = [
            $basePath . '/AdapterInterface.php',
            $basePath . '/BaseAdapter.php',
            $basePath . '/AdapterRegistry.php',
        ];

        foreach ($coreFiles as $file) {
            if (file_exists($file) && !class_exists($this->coreClassForFile($file), false)) {
                require_once $file;
            }
        }
    }

    /**
     * Map a core file path to its class name for class_exists checks.
     */
    private function coreClassForFile(string $file): string
    {
        $map = [
            'AdapterInterface.php' => 'ThailandTogether\\Adapters\\AdapterInterface',
            'BaseAdapter.php'      => 'ThailandTogether\\Adapters\\BaseAdapter',
            'AdapterRegistry.php'  => 'ThailandTogether\\Adapters\\AdapterRegistry',
        ];

        return $map[basename($file)] ?? '';
    }

    /**
     * Resolve the absolute path to the adapters/ directory.
     */
    private function getAdaptersPath(): string
    {
        // The adapters/ directory sits one level above the platform/ directory.
        return dirname(base_path()) . '/adapters';
    }

    /**
     * List all registered adapters with their status.
     *
     * @return array<int, array{name: string, version: string, available: bool}>
     */
    public function listAdapters(): array
    {
        $this->ensureDiscovered();

        return $this->registry->all();
    }

    /**
     * Get a specific adapter by name.
     *
     * @throws \InvalidArgumentException
     */
    public function getAdapter(string $name): AdapterInterface
    {
        $this->ensureDiscovered();

        return $this->registry->get($name);
    }

    /**
     * Check if an adapter is registered.
     */
    public function hasAdapter(string $name): bool
    {
        $this->ensureDiscovered();

        return $this->registry->has($name);
    }

    /**
     * Run a health check on a specific adapter.
     *
     * @return array{status: string, message: string}
     */
    public function healthCheck(string $name): array
    {
        $adapter = $this->getAdapter($name);

        try {
            return $adapter->healthCheck();
        } catch (\Throwable $e) {
            return [
                'status'  => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Run health checks on all adapters.
     *
     * @return array<string, array{status: string, message: string}>
     */
    public function healthCheckAll(): array
    {
        $this->ensureDiscovered();

        return $this->registry->healthCheckAll();
    }

    /**
     * Execute an action on a named adapter.
     *
     * @param string               $adapterName
     * @param string               $action
     * @param array<string, mixed> $params
     *
     * @return mixed
     */
    public function execute(string $adapterName, string $action, array $params = []): mixed
    {
        $adapter = $this->getAdapter($adapterName);

        return $adapter->execute($action, $params);
    }

    /**
     * Get the underlying registry (for advanced usage).
     */
    public function getRegistry(): AdapterRegistry
    {
        $this->ensureDiscovered();

        return $this->registry;
    }
}
