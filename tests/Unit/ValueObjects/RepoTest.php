<?php

declare(strict_types=1);

use Bladecn\Enums\SourceControl;
use Bladecn\ValueObjects\Repo;
use Illuminate\Support\Facades\Config;

it('returns the correct raw content url from given source control', function (Repo $repo) {
    expect($repo->rawContentUrl())->toBe(match ($repo->source) {
        SourceControl::GITHUB => 'https://raw.githubusercontent.com/vendor/repo/main',
        SourceControl::GITLAB => 'https://gitlab.com/vendor/repo/-/raw/main',
        SourceControl::BITBUCKET => 'https://bitbucket.org/vendor/repo/raw/main',
    });
})->with([
    fn () => new Repo(source: SourceControl::GITHUB, owner: 'vendor', repo: 'repo'),
    fn () => new Repo(source: SourceControl::GITLAB, owner: 'vendor', repo: 'repo'),
    fn () => new Repo(source: SourceControl::BITBUCKET, owner: 'vendor', repo: 'repo'),
]);

it('returns the correct raw content url from given source control if the default branch isset in the config', function (Repo $repo) {
    expect($repo->rawContentUrl())->toBe(match ($repo->source) {
        SourceControl::GITHUB => 'https://raw.githubusercontent.com/vendor/repo/develop',
        SourceControl::GITLAB => 'https://gitlab.com/vendor/repo/-/raw/develop',
        SourceControl::BITBUCKET => 'https://bitbucket.org/vendor/repo/raw/develop',
    });
})->with([
    function () {
        Config::set('bladecn.source.github.branch', 'develop');

        return new Repo(source: SourceControl::GITHUB, owner: 'vendor', repo: 'repo');
    },
    function () {
        Config::set('bladecn.source.gitlab.branch', 'develop');

        return new Repo(source: SourceControl::GITLAB, owner: 'vendor', repo: 'repo');
    },
    function () {
        Config::set('bladecn.source.bitbucket.branch', 'develop');

        return new Repo(source: SourceControl::BITBUCKET, owner: 'vendor', repo: 'repo');
    },
]);
