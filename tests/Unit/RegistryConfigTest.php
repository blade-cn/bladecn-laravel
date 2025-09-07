<?php

declare(strict_types=1);

use Bladecn\Exceptions\AlreadyExistsRemoteUrlException;
use Bladecn\RegistryConfig;
use Bladecn\ValueObjects\Config;

beforeEach(function () {
    $this->path = RegistryConfig::path();
    if (file_exists($this->path)) {
        unlink($this->path);
    }

    RegistryConfig::flush();
});

it('should have a default configuration', function () {
    $registryConfig = RegistryConfig::make();

    expect($registryConfig->config->registries)->toBeArray()->toBeEmpty();
});

it('should be a singleton', function () {
    $configA = RegistryConfig::make();
    $configB = RegistryConfig::make();

    expect($configA)->toBe($configB);
});

it('should intialize the configuration file', function () {
    $path = RegistryConfig::path();

    $result = RegistryConfig::init();

    expect($result)->toBeTrue();
    expect(file_exists($path))->toBeTrue();
});

it('should not initialize if the file exists', function () {
    $path = RegistryConfig::path();

    file_put_contents($path, json_encode(new Config));

    $result = RegistryConfig::init();

    expect($result)->toBeFalse();
});

it('should return the correct path', function () {
    $path = RegistryConfig::path();
    $expectedPath = getcwd() . '/bladecn-registry.json';

    expect($path)->toBeString()->toBe($expectedPath);
});

it('should persist the registry', function () {
    $registryConfig = RegistryConfig::make();

    $registryConfig->persist('https://github.com/blade-cn/example-registry');

    expect($registryConfig->config->registries)->toBeArray()->toHaveCount(1);
    expect($registryConfig->config->registries[0])->toBe('https://github.com/blade-cn/example-registry');
});

it('should not persists given registry if already exists', function () {
    $config = RegistryConfig::make();

    $config->persist('https://github.com/blade-cn/example-registry');
    $config->persist('https://github.com/blade-cn/example-registry');
})->throws(AlreadyExistsRemoteUrlException::class);

it('should flush the singleton instance', function () {
    $configA = RegistryConfig::make();

    RegistryConfig::flush();

    $configB = RegistryConfig::make();

    expect($configA)->not->toBe($configB);
});
