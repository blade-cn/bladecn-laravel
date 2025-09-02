<?php

declare(strict_types=1);

use Bladecn\RegistryConfig;

it('returns the correct project base path', function () {
    $expectedPath = getcwd() . '/bladecn-registry.json';
    $actualPath = RegistryConfig::path();

    expect($actualPath)->toBe($expectedPath);
});
