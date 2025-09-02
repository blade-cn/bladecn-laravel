<?php

declare(strict_types=1);

use Bladecn\Enums\SourceControl;

it('should return enum type based from the url', function (string $url) {
    expect(SourceControl::fromUrl($url))->toBeInstanceOf(SourceControl::class);
})->with([
    'https://github.com/vendor/repo',
    'https://gitlab.com/vendor/repo',
    'https://bitbucket.org/vendor/repo',
]);

it('should return null for unsupported url', function (string $url) {
    expect(SourceControl::fromUrl($url))->toBeNull();
})->with([
    'https://example.com/vendor/repo',
    'https://unknown.com/vendor/repo',
]);
