<?php

declare(strict_types=1);

namespace Bladecn\Enums;

enum SourceControl: string
{
    case GITHUB = 'github';
    case GITLAB = 'gitlab';
    case BITBUCKET = 'bitbucket';

    public static function fromUrl(string $url): ?self
    {
        return match (true) {
            str_contains($url, 'github.com') => self::GITHUB,
            str_contains($url, 'gitlab.com') => self::GITLAB,
            str_contains($url, 'bitbucket.org') => self::BITBUCKET,
            default => null,
        };
    }

    /** @return array<int, string> */
    public static function toArray(): array
    {
        return array_map(fn (self $enum) => $enum->value, self::cases());
    }
}
