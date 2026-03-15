<?php

namespace App\Services\ApiGateway\Adapters;

/**
 * Cloud Point / Reward loyalty point adapter.
 * Handles earn, redeem, transfer, and exchange operations.
 */
class CloudPointAdapter extends BaseAdapter
{
    public function providerName(): string
    {
        return $this->config['provider_name'] ?? 'Cloud Point';
    }

    /**
     * Earn reward points for a user.
     */
    public function earn(string $userId, int $points, string $reason, array $meta = []): mixed
    {
        return $this->execute('POST', '/points/earn', array_merge([
            'user_id' => $userId,
            'points' => $points,
            'reason' => $reason,
        ], $meta));
    }

    /**
     * Redeem reward points.
     */
    public function redeem(string $userId, int $points, string $rewardCode, array $meta = []): mixed
    {
        return $this->execute('POST', '/points/redeem', array_merge([
            'user_id' => $userId,
            'points' => $points,
            'reward_code' => $rewardCode,
        ], $meta));
    }

    /**
     * Get point balance for a user.
     */
    public function getBalance(string $userId): mixed
    {
        return $this->execute('GET', "/points/balance/{$userId}");
    }

    /**
     * Transfer points between users.
     */
    public function transfer(string $fromUserId, string $toUserId, int $points): mixed
    {
        return $this->execute('POST', '/points/transfer', [
            'from_user_id' => $fromUserId,
            'to_user_id' => $toUserId,
            'points' => $points,
        ]);
    }

    /**
     * Get transaction history for a user.
     */
    public function history(string $userId, int $limit = 50): mixed
    {
        return $this->execute('GET', "/points/history/{$userId}", [
            'limit' => $limit,
        ]);
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
