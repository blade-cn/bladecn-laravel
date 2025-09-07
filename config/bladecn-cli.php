<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Source Control Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the default branch and access token for each
    | supported source control provider used by your application. This allows
    | you to specify which branch should be used by default when interacting
    | with repositories, as well as provide an access token for private
    | repositories if necessary.
    |
    | Supported providers: "github", "gitlab", "bitbucket"
    |
    | For each provider, you may set the default branch name and, if the
    | repository is private, an access token to authenticate API requests.
    | These values can be set via environment variables for convenience
    | and security.
    |
    */
    'source' => [
        'github' => [
            'branch' => env('BLADECN_GITHUB_DEFAULT_BRANCH', 'main'),
            'token' => env('BLADECN_GITHUB_ACCESS_TOKEN', null),
        ],

        'gitlab' => [
            'branch' => env('BLADECN_GITLAB_DEFAULT_BRANCH', 'main'),
            'token' => env('BLADECN_GITLAB_ACCESS_TOKEN', null),
        ],

        'bitbucket' => [
            'branch' => env('BLADECN_BITBUCKET_DEFAULT_BRANCH', 'main'),
            'token' => env('BLADECN_BITBUCKET_ACCESS_TOKEN', null),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Registry Cache
    |--------------------------------------------------------------------------
    |
    | This value controls how long (in seconds) the registry data will be cached
    | by the application. You may adjust this value to increase or decrease the
    | cache duration for registry lookups. A higher value reduces API calls,
    | while a lower value ensures fresher data.
    |
    */
    'cache_ttl' => env('BLADECN_CACHE_TTL', 86400), // 24 hours
];
