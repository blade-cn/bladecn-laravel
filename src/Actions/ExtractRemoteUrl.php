<?php

declare(strict_types=1);

namespace Bladecn\Actions;

use Bladecn\Enums\SourceControl;
use Bladecn\Exceptions\InvalidRemoteUrlException;
use Bladecn\ValueObjects\Repo;

class ExtractRemoteUrl
{
    public function __invoke(string $url): Repo
    {
        $patterns = [
            'github' => '#github\.com/([^/]+)/([^/]+)#',
            'gitlab' => '#gitlab\.com/([^/]+)/([^/]+)#',
            'bitbucket' => '#bitbucket\.org/([^/]+)/([^/]+)#',
        ];

        foreach ($patterns as $source => $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return new Repo(
                    source: SourceControl::tryFrom($source),
                    owner: $matches[1],
                    repo: $matches[2],
                );
            }
        }

        throw new InvalidRemoteUrlException;
    }
}
