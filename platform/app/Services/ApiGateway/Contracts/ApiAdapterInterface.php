<?php

namespace App\Services\ApiGateway\Contracts;

use App\Models\Integration\ApiCredential;

/**
 * Interface for all third-party API adapters.
 * Each provider (Stripe, Twilio, etc.) implements this.
 */
interface ApiAdapterInterface
{
    /**
     * Initialize with credentials and config.
     */
    public function __construct(?ApiCredential $credential, array $config = []);

    /**
     * Execute an API call.
     */
    public function execute(string $method, string $endpoint, array $data = []): mixed;

    /**
     * Check if the provider is properly configured and reachable.
     */
    public function healthCheck(): bool;

    /**
     * Get the provider name.
     */
    public function providerName(): string;
}
