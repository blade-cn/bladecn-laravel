<?php

declare(strict_types=1);

namespace Bladecn\Actions\Validations;

use Bladecn\Enums\SourceControl;
use Closure;
use Illuminate\Contracts\Validation\Validator as ValidationContract;
use Illuminate\Support\Facades\Validator;

class ValidateRegistryUrl
{
    public function __invoke(string $url): ValidationContract
    {
        return Validator::make(
            ['url' => $url],
            [
                'url' => [
                    'required',
                    'url:https',
                    function (string $attribute, mixed $value, Closure $fail) {
                        if (! is_string($value) || SourceControl::fromUrl($value) === null) {
                            $fail('The URL must be from a supported source control platform (GitHub, GitLab, Bitbucket).');
                        }
                    },
                ],
            ],
            [
                'url.required' => 'The URL is required.',
                'url.url' => 'The URL must be a valid URL.',
            ]
        );
    }
}
