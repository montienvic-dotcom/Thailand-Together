<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Cluster
    |--------------------------------------------------------------------------
    |
    | The default cluster slug used when no cluster context is specified.
    | In production, this should be null (require explicit cluster).
    |
    */
    'default' => env('DEFAULT_CLUSTER', null),

    /*
    |--------------------------------------------------------------------------
    | Cluster Resolution Order
    |--------------------------------------------------------------------------
    |
    | How the ClusterAware middleware resolves which cluster to use.
    | Options: header, subdomain, parameter, session
    |
    */
    'resolution' => ['header', 'parameter', 'subdomain'],

    /*
    |--------------------------------------------------------------------------
    | Subdomain Pattern
    |--------------------------------------------------------------------------
    |
    | Pattern for extracting cluster slug from subdomain.
    | e.g., {cluster}.thailandtogether.com
    |
    */
    'subdomain_base' => env('CLUSTER_SUBDOMAIN_BASE', 'thailandtogether.com'),

    /*
    |--------------------------------------------------------------------------
    | Cross-Cluster Settings
    |--------------------------------------------------------------------------
    */
    'cross_cluster' => [
        // Allow reward point transfers between clusters
        'reward_transfer' => env('CROSS_CLUSTER_REWARD_TRANSFER', true),

        // Default exchange rate when no specific rate is set
        'default_exchange_rate' => 1.0,

        // Enable cross-cluster recommendations
        'recommendations' => env('CROSS_CLUSTER_RECOMMENDATIONS', true),
    ],

];
