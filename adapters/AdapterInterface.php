<?php

declare(strict_types=1);

namespace ThailandTogether\Adapters;

/**
 * Interface that all external app adapters must implement.
 *
 * Each adapter wraps an external system (hotel management, tour booking, etc.)
 * and exposes a unified action-based API for the Thailand Together platform.
 */
interface AdapterInterface
{
    /**
     * Get the human-readable name of this adapter.
     */
    public function getName(): string;

    /**
     * Get the adapter version string (semver recommended).
     */
    public function getVersion(): string;

    /**
     * Check whether the external system is currently reachable.
     */
    public function isAvailable(): bool;

    /**
     * Run a detailed health check against the external system.
     *
     * @return array{status: string, message: string, details?: array}
     */
    public function healthCheck(): array;

    /**
     * Apply runtime configuration to this adapter.
     *
     * @param array<string, mixed> $config
     */
    public function configure(array $config): void;

    /**
     * Execute a named action on the external system.
     *
     * @param string              $action The action identifier (e.g. "listRooms")
     * @param array<string, mixed> $params Action-specific parameters
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException When the action is not supported
     * @throws \RuntimeException         When execution fails
     */
    public function execute(string $action, array $params = []): mixed;

    /**
     * List every action this adapter supports.
     *
     * @return string[]
     */
    public function getSupportedActions(): array;
}
