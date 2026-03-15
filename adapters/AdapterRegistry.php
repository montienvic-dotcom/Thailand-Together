<?php

declare(strict_types=1);

namespace ThailandTogether\Adapters;

/**
 * Registry that discovers, registers and manages all external-app adapters.
 */
class AdapterRegistry
{
    /** @var array<string, AdapterInterface> */
    private array $adapters = [];

    /** @var array<string, class-string<AdapterInterface>> */
    private array $adapterClasses = [];

    /**
     * Auto-discover adapter classes from subdirectories of a given base path.
     *
     * Convention: each subdirectory contains one *Adapter.php file whose
     * fully-qualified class name follows the pattern
     * ThailandTogether\Adapters\{SubNamespace}\{ClassName}.
     *
     * @param string $basePath Absolute path to the adapters/ directory
     */
    public function discover(string $basePath): void
    {
        if (!is_dir($basePath)) {
            return;
        }

        $directories = glob($basePath . '/*', GLOB_ONLYDIR);

        if ($directories === false) {
            return;
        }

        foreach ($directories as $dir) {
            $adapterFiles = glob($dir . '/*Adapter.php');

            if ($adapterFiles === false || $adapterFiles === []) {
                continue;
            }

            foreach ($adapterFiles as $file) {
                $this->registerFromFile($file, $dir);
            }
        }
    }

    /**
     * Register an adapter from a PHP file.
     */
    private function registerFromFile(string $filePath, string $directory): void
    {
        $className = $this->resolveClassName($filePath, $directory);

        if ($className === null) {
            return;
        }

        // Require the file if the class is not yet loaded.
        if (!class_exists($className, false)) {
            require_once $filePath;
        }

        if (!class_exists($className) || !is_subclass_of($className, AdapterInterface::class)) {
            return;
        }

        /** @var AdapterInterface $instance */
        $instance = new $className();
        $this->adapters[$instance->getName()] = $instance;
        $this->adapterClasses[$instance->getName()] = $className;
    }

    /**
     * Resolve the fully-qualified class name from a file path.
     *
     * Reads the file to extract the namespace and class name.
     */
    private function resolveClassName(string $filePath, string $directory): ?string
    {
        $contents = file_get_contents($filePath);

        if ($contents === false) {
            return null;
        }

        $namespace = null;
        $class = null;

        if (preg_match('/namespace\s+([^;]+);/', $contents, $nsMatch)) {
            $namespace = $nsMatch[1];
        }

        if (preg_match('/class\s+(\w+)/', $contents, $classMatch)) {
            $class = $classMatch[1];
        }

        if ($namespace === null || $class === null) {
            return null;
        }

        return $namespace . '\\' . $class;
    }

    /**
     * Manually register an adapter instance.
     */
    public function register(AdapterInterface $adapter): void
    {
        $this->adapters[$adapter->getName()] = $adapter;
    }

    /**
     * Get an adapter by name.
     *
     * @throws \InvalidArgumentException When the adapter is not registered
     */
    public function get(string $name): AdapterInterface
    {
        if (!isset($this->adapters[$name])) {
            throw new \InvalidArgumentException(
                "Adapter [{$name}] is not registered. Available: "
                . implode(', ', array_keys($this->adapters))
            );
        }

        return $this->adapters[$name];
    }

    /**
     * Check whether an adapter with the given name is registered.
     */
    public function has(string $name): bool
    {
        return isset($this->adapters[$name]);
    }

    /**
     * List all registered adapters with basic metadata.
     *
     * @return array<int, array{name: string, version: string, available: bool}>
     */
    public function all(): array
    {
        $list = [];

        foreach ($this->adapters as $adapter) {
            $list[] = [
                'name'      => $adapter->getName(),
                'version'   => $adapter->getVersion(),
                'available' => $adapter->isAvailable(),
            ];
        }

        return $list;
    }

    /**
     * Run health checks on every registered adapter.
     *
     * @return array<string, array{status: string, message: string}>
     */
    public function healthCheckAll(): array
    {
        $results = [];

        foreach ($this->adapters as $name => $adapter) {
            try {
                $results[$name] = $adapter->healthCheck();
            } catch (\Throwable $e) {
                $results[$name] = [
                    'status'  => 'error',
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Get the names of all registered adapters.
     *
     * @return string[]
     */
    public function names(): array
    {
        return array_keys($this->adapters);
    }
}
