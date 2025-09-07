<?php

declare(strict_types=1);

namespace Bladecn\Actions\Validations;

use Illuminate\Contracts\Validation\Validator as ValidationContract;
use Illuminate\Support\Facades\Validator;

class ValidateConfigData
{
    /** @param  array<string, mixed>  $data */
    public function __invoke(array $data): ValidationContract
    {
        return Validator::make($data, [
            'name' => 'required|string',
            'homepage' => 'nullable|url',
            'authors' => 'nullable|array',
            'authors.*' => 'string',
            'description' => 'nullable|string',
            'version' => 'required|string',
            'last_updated' => 'sometimes|date',

            'items' => 'required|array',
            'items.*.name' => 'required|string',
            'items.*.type' => 'required|string',
            'items.*.description' => 'nullable|string',
            'items.*.tags' => 'nullable|array',
            'items.*.tags.*' => 'string',
            'items.*.version' => 'required|string',
            'items.*.nodeDependencies' => 'nullable|array',
            'items.*.dependencies' => 'nullable|array',
            'items.*.files' => 'required|array',
            'items.*.cssVars' => 'nullable|array',
        ], [
            'name.required' => 'The registry schema name is required.',
            'name.string' => 'The registry schema name must be a string.',
            'url.required' => 'The registry schema URL is required.',
            'url.url' => 'The registry schema URL must be a valid URL.',
            'description.required' => 'The registry schema description is required.',
            'description.string' => 'The registry schema description must be a string.',
            'items.required' => 'The registry schema items are required.',
            'items.array' => 'The registry schema items must be an array.',
            'items.*.name.required' => 'Each item must have a name.',
            'items.*.name.string' => 'Each item name must be a string.',
            'items.*.type.required' => 'Each item must have a type.',
            'items.*.type.string' => 'Each item type must be a string.',
            'items.*.version.required' => 'Each item must have a version.',
            'items.*.version.string' => 'Each item version must be a string.',
            'items.*.files.required' => 'Each item must have files defined.',
            'items.*.files.array' => 'Each item files must be an array.',
            'items.*.tags.array' => 'Each item tags must be an array of strings.',
            'items.*.tags.*.string' => 'Each item tag must be a string.',
            'items.*.nodeDependencies.array' => 'Each item nodeDependencies must be an array of strings.',
            'items.*.nodeDependencies.*.string' => 'Each item nodeDependency must be a string.',
            'items.*.dependencies.array' => 'Each item dependencies must be an array of strings.',
            'items.*.dependencies.*.string' => 'Each item dependency must be a string.',
            'items.*.cssVars.array' => 'Each item cssVars must be an array of strings.',
            'items.*.cssVars.*.string' => 'Each item cssVar must be a string.',
        ]);
    }
}
