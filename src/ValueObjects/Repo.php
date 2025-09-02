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
            SourceControl::GITHUB => config('bladecn.source.github.branch', 'main'),
            SourceControl::GITLAB => config('bladecn.source.gitlab.branch', 'main'),
            SourceControl::BITBUCKET => config('bladecn.source.bitbucket.branch', 'main'),
        };

        return match ($this->source) {
            SourceControl::GITHUB => "https://raw.githubusercontent.com/{$this->owner}/{$this->repo}/{$branch}",
            SourceControl::GITLAB => "https://gitlab.com/{$this->owner}/{$this->repo}/-/raw/{$branch}",
            SourceControl::BITBUCKET => "https://bitbucket.org/{$this->owner}/{$this->repo}/raw/{$branch}",
        };
    }
}
