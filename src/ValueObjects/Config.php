<?php

declare(strict_types=1);

namespace Bladecn\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;

readonly class Config implements Arrayable
{
    /** @param  array<string>  $registries */
    public function __construct(
        public array $registries = [],
    ) {
        //
    }

    public static function from(array $data): self
    {
        return new self(
            registries: $data['registries'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'registries' => $this->registries,
        ];
    }
}
