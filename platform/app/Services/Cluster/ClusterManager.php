<?php

namespace App\Services\Cluster;

use App\Models\Global\Cluster;
use App\Models\Global\Country;
use Illuminate\Support\Facades\Config;

/**
 * Manages current cluster context throughout the request lifecycle.
 * Set by ClusterAware middleware, used everywhere else.
 */
class ClusterManager
{
    private ?Cluster $currentCluster = null;
    private ?Country $currentCountry = null;

    /**
     * Set the active cluster for this request.
     */
    public function setCluster(Cluster $cluster): void
    {
        $this->currentCluster = $cluster;
        $this->currentCountry = $cluster->country;

        // Switch database connection if cluster has its own
        if ($cluster->database_connection) {
            Config::set('database.default', $cluster->database_connection);
        }

        // Set timezone
        Config::set('app.timezone', $cluster->timezone);

        // Set locale
        if ($cluster->default_locale) {
            app()->setLocale($cluster->default_locale);
        }
    }

    public function current(): ?Cluster
    {
        return $this->currentCluster;
    }

    public function currentId(): ?int
    {
        return $this->currentCluster?->id;
    }

    public function country(): ?Country
    {
        return $this->currentCountry;
    }

    public function countryId(): ?int
    {
        return $this->currentCountry?->id;
    }

    public function isSet(): bool
    {
        return $this->currentCluster !== null;
    }

    /**
     * Run a callback in the context of a specific cluster.
     */
    public function runInCluster(Cluster $cluster, callable $callback): mixed
    {
        $previous = $this->currentCluster;
        $this->setCluster($cluster);

        try {
            return $callback($cluster);
        } finally {
            if ($previous) {
                $this->setCluster($previous);
            } else {
                $this->currentCluster = null;
                $this->currentCountry = null;
            }
        }
    }
}
