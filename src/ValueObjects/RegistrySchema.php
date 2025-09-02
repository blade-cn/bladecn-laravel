<?php

declare(strict_types=1);

namespace Bladecn\ValueObjects;

final readonly class RegistrySchema
{
    /**
     * @param  array<RemoteRegistry>  $registries
     */
    public function __construct(
        public array $registries,
    ) {
        //
    }
}
