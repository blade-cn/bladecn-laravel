<?php

declare(strict_types=1);

use Bladecn\Exceptions\AlreadyExistsRemoteUrlException;
use Bladecn\RegistryConfig;
use Bladecn\ValueObjects\RemoteRegistry;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->path = RegistryConfig::path();
    if (file_exists($this->path)) {
        unlink($this->path);
    }

    RegistryConfig::flush();
});

it('should have a default configuration', function () {
    $config = RegistryConfig::make();

    expect($config->registries)->toBeArray()->toBeEmpty();
});

it('should be a singleton', function () {
    $configA = RegistryConfig::make();
    $configB = RegistryConfig::make();

    expect($configA)->toBe($configB);
});

it('should intialize the configuration file', function () {
    $path = RegistryConfig::path();

    if (file_exists($path)) {
        unlink($path);
    }

    $result = RegistryConfig::init();

    expect($result)->toBeTrue();
    expect(file_exists($path))->toBeTrue();
});

it('should not initialize if the file exists', function () {
    $path = RegistryConfig::path();

    file_put_contents($path, '{"registries":[]}');

    $result = RegistryConfig::init();

    expect($result)->toBeFalse();
});

it('should return the correct path', function () {
    $path = RegistryConfig::path();
    $expectedPath = getcwd() . '/bladecn-registry.json';

    expect($path)->toBeString()->toBe($expectedPath);
});

it('should correctly read existing configuration', function () {
    $lastUpdated = Carbon::now();
    $config = RegistryConfig::make();

    $config->persist(RemoteRegistry::from([
        'name' => 'Sample Registry',
        'url' => 'https://github.com/bladecn/bladecn-registry',
        'description' => 'A sample registry',
        'lastUpdated' => $lastUpdated->toIso8601String(),
    ]));

    /** @var RemoteRegistry $registry */
    $registry = $config->registries[0];

    expect($config->registries)->toBeArray()->toHaveCount(1);
    expect($registry->name)->toBe('Sample Registry');
    expect($registry->url)->toBe('https://github.com/bladecn/bladecn-registry');
    expect($registry->description)->toBe('A sample registry');
    expect($registry->lastUpdated->format('c'))->toBe($lastUpdated->format('c'));
});

it('should not add duplicate registries', function () {
    $config = RegistryConfig::make();

    $registryData = [
        'name' => 'Sample Registry',
        'url' => 'https://github.com/bladecn/bladecn-registry',
        'description' => 'A sample registry',
        'lastUpdated' => Carbon::now()->toIso8601String(),
    ];

    $config->persist(RemoteRegistry::from($registryData));
    $config->persist(RemoteRegistry::from($registryData));
})->throws(AlreadyExistsRemoteUrlException::class);

it('should flush the singleton instance', function () {
    $configA = RegistryConfig::make();
    RegistryConfig::flush();
    $configB = RegistryConfig::make();

    expect($configA)->not->toBe($configB);
});