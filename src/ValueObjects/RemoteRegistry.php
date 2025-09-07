<?php

declare(strict_types=1);

namespace Bladecn\ValueObjects;

use Illuminate\Support\Carbon;

final readonly class RemoteRegistry
{
    /**
     * @param  array<int, string>  $authors
     * @param  RegistryItem[]  $items
     */
    public function __construct(
        public string $name,
        public string $homepage,
        public string $description,
        public array $authors,
        public string $version,
        public Carbon $lastUpdated,
        public array $items = [],
    ) {
        //
    }

    /** @param array<string, mixed> $data */
    public static function from(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            homepage: $data['homepage'] ?? '',
            description: $data['description'] ?? '',
            authors: $data['authors'] ?? [],
            version: $data['version'] ?? '1.0.0',
            lastUpdated: isset($data['lastUpdated']) ? Carbon::parse($data['lastUpdated']) : now(),
            items: isset($data['items']) ? array_map(fn (array $item) => RegistryItem::from($item), $data['items']) : [],
        );
    }
}
