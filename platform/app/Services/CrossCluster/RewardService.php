<?php

namespace App\Services\CrossCluster;

use App\Models\Auth\User;
use Illuminate\Support\Facades\DB;

/**
 * Manages reward points across clusters and countries.
 * Supports earning, redeeming, transferring between clusters
 * with exchange rate conversion.
 */
class RewardService
{
    /**
     * Get or create a reward wallet for a user in a cluster.
     */
    public function getWallet(int $userId, int $clusterId, string $currency = 'POINT'): object
    {
        return DB::table('reward_wallets')->updateOrInsert(
            ['user_id' => $userId, 'cluster_id' => $clusterId, 'currency' => $currency],
            ['balance' => DB::raw('COALESCE(balance, 0)'), 'updated_at' => now()]
        ) ? DB::table('reward_wallets')
            ->where('user_id', $userId)
            ->where('cluster_id', $clusterId)
            ->where('currency', $currency)
            ->first()
        : null;
    }

    /**
     * Earn points in a specific cluster.
     */
    public function earn(int $userId, int $clusterId, float $amount, string $referenceType, ?int $referenceId = null, ?string $description = null): bool
    {
        return DB::transaction(function () use ($userId, $clusterId, $amount, $referenceType, $referenceId, $description) {
            $wallet = $this->getWallet($userId, $clusterId);

            $newBalance = $wallet->balance + $amount;

            DB::table('reward_wallets')
                ->where('id', $wallet->id)
                ->update(['balance' => $newBalance, 'updated_at' => now()]);

            DB::table('reward_transactions')->insert([
                'user_id' => $userId,
                'wallet_id' => $wallet->id,
                'type' => 'earn',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'source_cluster_id' => $clusterId,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return true;
        });
    }

    /**
     * Redeem points in a specific cluster.
     */
    public function redeem(int $userId, int $clusterId, float $amount, string $referenceType, ?int $referenceId = null, ?string $description = null): bool
    {
        return DB::transaction(function () use ($userId, $clusterId, $amount, $referenceType, $referenceId, $description) {
            $wallet = $this->getWallet($userId, $clusterId);

            if ($wallet->balance < $amount) {
                return false;
            }

            $newBalance = $wallet->balance - $amount;

            DB::table('reward_wallets')
                ->where('id', $wallet->id)
                ->update(['balance' => $newBalance, 'updated_at' => now()]);

            DB::table('reward_transactions')->insert([
                'user_id' => $userId,
                'wallet_id' => $wallet->id,
                'type' => 'redeem',
                'amount' => -$amount,
                'balance_after' => $newBalance,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'source_cluster_id' => $clusterId,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return true;
        });
    }

    /**
     * Transfer points between clusters with exchange rate.
     * e.g., Transfer 100 points from Pattaya to Danang at 1.2 rate = 120 Danang points.
     */
    public function transferBetweenClusters(int $userId, int $fromClusterId, int $toClusterId, float $amount): bool
    {
        return DB::transaction(function () use ($userId, $fromClusterId, $toClusterId, $amount) {
            $rate = $this->getExchangeRate($fromClusterId, $toClusterId);
            if ($rate === null) {
                return false;
            }

            $fromWallet = $this->getWallet($userId, $fromClusterId);
            if ($fromWallet->balance < $amount) {
                return false;
            }

            $convertedAmount = $amount * $rate;
            $toWallet = $this->getWallet($userId, $toClusterId);

            // Deduct from source
            $fromNewBalance = $fromWallet->balance - $amount;
            DB::table('reward_wallets')
                ->where('id', $fromWallet->id)
                ->update(['balance' => $fromNewBalance, 'updated_at' => now()]);

            DB::table('reward_transactions')->insert([
                'user_id' => $userId,
                'wallet_id' => $fromWallet->id,
                'type' => 'transfer_out',
                'amount' => -$amount,
                'balance_after' => $fromNewBalance,
                'reference_type' => 'transfer',
                'source_cluster_id' => $fromClusterId,
                'target_cluster_id' => $toClusterId,
                'description' => "Transfer to cluster #{$toClusterId} (rate: {$rate})",
                'metadata' => json_encode(['exchange_rate' => $rate, 'converted_amount' => $convertedAmount]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add to target
            $toNewBalance = $toWallet->balance + $convertedAmount;
            DB::table('reward_wallets')
                ->where('id', $toWallet->id)
                ->update(['balance' => $toNewBalance, 'updated_at' => now()]);

            DB::table('reward_transactions')->insert([
                'user_id' => $userId,
                'wallet_id' => $toWallet->id,
                'type' => 'transfer_in',
                'amount' => $convertedAmount,
                'balance_after' => $toNewBalance,
                'reference_type' => 'transfer',
                'source_cluster_id' => $fromClusterId,
                'target_cluster_id' => $toClusterId,
                'description' => "Transfer from cluster #{$fromClusterId} (rate: {$rate})",
                'metadata' => json_encode(['exchange_rate' => $rate, 'original_amount' => $amount]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return true;
        });
    }

    /**
     * Get exchange rate between two clusters.
     */
    public function getExchangeRate(int $fromClusterId, int $toClusterId): ?float
    {
        if ($fromClusterId === $toClusterId) {
            return 1.0;
        }

        $rate = DB::table('reward_exchange_rates')
            ->where('from_cluster_id', $fromClusterId)
            ->where('to_cluster_id', $toClusterId)
            ->where('is_active', true)
            ->value('rate');

        return $rate ? (float) $rate : null;
    }

    /**
     * Get total balance across all clusters for a user.
     */
    public function getTotalBalance(int $userId): array
    {
        return DB::table('reward_wallets')
            ->where('user_id', $userId)
            ->join('clusters', 'reward_wallets.cluster_id', '=', 'clusters.id')
            ->select('clusters.name as cluster_name', 'clusters.id as cluster_id', 'reward_wallets.balance', 'reward_wallets.currency')
            ->get()
            ->toArray();
    }
}
