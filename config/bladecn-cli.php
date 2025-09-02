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
];
