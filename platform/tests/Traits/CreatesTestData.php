<?php

namespace Tests\Traits;

use App\Models\Global\Cluster;
use App\Models\Global\Country;

trait CreatesTestData
{
    protected function createCountry(array $overrides = []): Country
    {
        return Country::create(array_merge([
            'name' => 'Thailand',
            'code' => 'THA',
            'code_alpha2' => 'TH',
            'is_active' => true,
        ], $overrides));
    }

    protected function createCluster(Country $country, array $overrides = []): Cluster
    {
        return Cluster::create(array_merge([
            'name' => 'Pattaya',
            'slug' => 'pattaya',
            'code' => 'PTY',
            'country_id' => $country->id,
            'is_active' => true,
            'timezone' => 'Asia/Bangkok',
        ], $overrides));
    }
}
