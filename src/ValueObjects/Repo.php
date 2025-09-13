<?php

declare(strict_types=1);

namespace Bladecn\ValueObjects;

use Bladecn\Enums\SourceControl;

final readonly class Repo
{
    public function __construct(
        public SourceControl $source,
        public string $owner,
        public string $repo
    ) {
        //
    }

    public function rawContentUrl(): string
    {
        $branch = match ($this->source) {
            SourceControl::GITHUB => config('bladecn-laravel.source.github.branch', 'main'),
            SourceControl::GITLAB => config('bladecn-laravel.source.gitlab.branch', 'main'),
            SourceControl::BITBUCKET => config('bladecn-laravel.source.bitbucket.branch', 'main'),
        };

        return match ($this->source) {
            SourceControl::GITHUB => "https://raw.githubusercontent.com/{$this->owner}/{$this->repo}/refs/heads/{$branch}",
            SourceControl::GITLAB => "https://gitlab.com/{$this->owner}/{$this->repo}/-/raw/{$branch}",
            SourceControl::BITBUCKET => "https://bitbucket.org/{$this->owner}/{$this->repo}/raw/{$branch}",
        };
    }

    public function accessToken(): ?string
    {
        return match ($this->source) {
            SourceControl::GITHUB => config('bladecn-laravel.source.github.token'),
            SourceControl::GITLAB => config('bladecn-laravel.source.gitlab.token'),
            SourceControl::BITBUCKET => config('bladecn-laravel.source.bitbucket.token'),
        };
    }
}
