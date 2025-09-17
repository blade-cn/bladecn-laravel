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
}
