<?php

declare(strict_types=1);

namespace Bladecn\ValueObjects;

use Illuminate\Support\Carbon;

final readonly class RemoteRegistry
{
    public function __construct(
        public string $name,
        public string $url,
        public string $description,
        public ?Carbon $lastUpdated = null,
    ) {
        //
    }

    /** @param array<string, string> $data */
    public static function from(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            url: $data['url'] ?? '',
            description: $data['description'] ?? '',
            lastUpdated: Carbon::parse($data['lastUpdated']) ?? '',
        );
    }
}
