<?php

declare(strict_types=1);

use Bladecn\Actions\ExtractRemoteUrl;
use Bladecn\Enums\SourceControl;
use Bladecn\Exceptions\InvalidRemoteUrlException;
use Bladecn\ValueObjects\Repo;

it('returns the resolved repo from the url', function (array $data) {
    ['url' => $url, 'source' => $source] = $data;

    $url = app(ExtractRemoteUrl::class)($url);

    expect($url)->toBeInstanceOf(Repo::class);
    expect($url)->toMatchObject([
        'source' => $source,
        'owner' => 'laravel',
        'repo' => 'bladecn-registy',
    ]);
})->with([
    fn () => ['url' => 'https://github.com/laravel/bladecn-registy', 'source' => SourceControl::GITHUB],
    fn () => ['url' => 'https://gitlab.com/laravel/bladecn-registy', 'source' => SourceControl::GITLAB],
    fn () => ['url' => 'https://bitbucket.org/laravel/bladecn-registy', 'source' => SourceControl::BITBUCKET],
]);

it('throws an exception for unsupported url', function () {
    app(ExtractRemoteUrl::class)('https://example.com/laravel/bladecn-registy');
})->throws(InvalidRemoteUrlException::class, 'The provided URL is not supported. Only GitHub, GitLab, and Bitbucket URLs are allowed.');
