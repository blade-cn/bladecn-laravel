<?php

declare(strict_types=1);

namespace Bladecn\ValueObjects;

use Bladecn\Enums\RegistryItemType;

final class RegistryItem
{
    /**
     * @param  array<int, string>  $tags
     * @param  array<string, string>  $nodeDependencies
     * @param  array<string, string>  $dependencies
     * @param  array<int, array{path: string, type: string}>  $files
     * @param  array<int, string>  $cssVars
     */
    public function __construct(
        public string $name,
        public RegistryItemType $type,
        public string $description,
        public array $tags,
        public string $version,
        public array $nodeDependencies,
        public array $dependencies,
        public array $files,
        public array $cssVars,
    ) {
        //
    }

    public static function from(array $data): self
    {
        return new self(
            name: $data['name'],
            type: RegistryItemType::tryFrom($data['type']) ?? RegistryItemType::COMPONENT,
            description: $data['description'] ?? '',
            tags: isset($data['tags']) && filled($data['tags']) ? $data['tags'] : [],
            version: $data['version'] ?? '1.0.0',
            nodeDependencies: isset($data['nodeDependencies']) && filled($data['nodeDependencies']) ? $data['nodeDependencies'] : [],
            dependencies: isset($data['dependencies']) && filled($data['dependencies']) ? $data['dependencies'] : [],
            files: isset($data['files']) && filled($data['files']) ? $data['files'] : [],
            cssVars: isset($data['cssVars']) && filled($data['cssVars']) ? $data['cssVars'] : [],
        );
    }
}
