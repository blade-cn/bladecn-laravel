<?php

declare(strict_types=1);

namespace Bladecn\Actions;

use Illuminate\Contracts\Validation\Validator as ValidationContract;
use Illuminate\Support\Facades\Validator;

class ValidateRemoteRegistryData
{
    /** @param  array<string, mixed>  $data */
    public function __invoke(array $data): ValidationContract
    {
        return Validator::make($data, [
            'name' => 'required|string',
            'url' => 'required|url',
            'description' => 'required|string',
            'version' => 'sometimes|string',
            'lastUpdated' => 'sometimes|date',
        ], [
            'name.required' => 'The registry schema name is required.',
            'name.string' => 'The registry schema name must be a string.',
            'url.required' => 'The registry schema URL is required.',
            'url.url' => 'The registry schema URL must be a valid URL.',
            'description.required' => 'The registry schema description is required.',
            'description.string' => 'The registry schema description must be a string.',
        ]);
    }
}
