<?php

namespace App\Services\CrossCluster;

use Illuminate\Support\Facades\DB;

/**
 * Cross-cluster recommendation engine.
 * Recommends destinations, journeys, and merchants from other clusters
 * based on user preferences, behavior, and active campaigns.
 */
class RecommendationService
{
    /**
     * Get cross-cluster recommendations for a user.
     * Suggests destinations in other clusters based on their activity.
     */
    public function getRecommendations(int $userId, int $currentClusterId, int $limit = 5): array
    {
        return DB::table('cross_cluster_recommendations')
            ->where('cross_cluster_recommendations.from_cluster_id', $currentClusterId)
            ->where('cross_cluster_recommendations.is_active', true)
            ->join('clusters', 'cross_cluster_recommendations.to_cluster_id', '=', 'clusters.id')
            ->select(
                'cross_cluster_recommendations.*',
                'clusters.name as target_cluster_name',
                'clusters.slug as target_cluster_slug'
            )
            ->orderBy('cross_cluster_recommendations.priority', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Create a cross-cluster recommendation.
     */
    public function createRecommendation(array $data): int
    {
        return DB::table('cross_cluster_recommendations')->insertGetId([
            'from_cluster_id' => $data['source_cluster_id'],
            'to_cluster_id' => $data['target_cluster_id'],
            'type' => $data['type'] ?? 'destination',
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'content' => isset($data['content']) ? json_encode($data['content']) : null,
            'priority' => $data['priority'] ?? 0,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Get popular journeys from other clusters that tourists might enjoy.
     */
    public function getPopularJourneysFromOtherClusters(int $currentClusterId, int $limit = 10): array
    {
        return DB::table('journey')
            ->where('journey.cluster_id', '!=', $currentClusterId)
            ->whereIn('journey.status', ['active', 'ACTIVE'])
            ->join('clusters', 'journey.cluster_id', '=', 'clusters.id')
            ->select(
                'journey.journey_code',
                'journey.journey_name_en',
                'journey.journey_name_th',
                'journey.journey_group',
                'journey.total_minutes_sum',
                'journey.gmv_per_person',
                'clusters.name as cluster_name',
                'clusters.slug as cluster_slug'
            )
            ->orderByDesc('journey.tp_total_normal')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get active cross-cluster campaigns.
     */
    public function getActiveCampaigns(int $clusterId): array
    {
        return DB::table('campaigns')
            ->where(function ($q) use ($clusterId) {
                $q->where('scope', 'global')
                  ->orWhere(function ($q2) use ($clusterId) {
                      $q2->where('scope', 'cluster')
                         ->where('cluster_id', $clusterId);
                  });
            })
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->orderByDesc('priority')
            ->get()
            ->toArray();
    }
}
