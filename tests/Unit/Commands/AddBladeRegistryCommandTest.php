<?php

declare(strict_types=1);

use Bladecn\RegistryConfig;
use Bladecn\ValueObjects\RemoteRegistry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\artisan;

beforeEach(function () {
    $this->path = RegistryConfig::path();

    if (file_exists($this->path)) {
        unlink($this->path);
    }

    RegistryConfig::flush();
});

it('adds a new BladeCN registry successfully', function () {
    Http::fake([
        'https://raw.githubusercontent.com/blade-cn/registry-example/main/bladecn-registry.json' => Http::response([
            'name' => 'BladeCN Registry',
            'url' => 'https://registry.bladecn.com',
            'description' => 'A registry for BladeCN packages.',
            'version' => '1.0.0',
            'lastUpdated' => now()->toDateTimeString(),
        ], 200),
    ]);

    artisan('bladecn:add-registry')
        ->expectsQuestion('Please enter your BladeCN registry URL:', 'https://github.com/blade-cn/registry-example')
        ->expectsOutputToContain('Registry [BladeCN Registry] added successfully.')
        ->assertSuccessful();

    expect(RegistryConfig::make()->registries)->toHaveCount(1);
});

it('should validate if the given url is from a supported source control platform', function () {
    artisan('bladecn:add-registry')
        ->expectsQuestion('Please enter your BladeCN registry URL:', 'https://google.com')
        ->expectsOutputToContain('The URL must be from a supported source control platform (GitHub, GitLab, Bitbucket).')
        ->assertFailed();
});

it('should validate if the given url uses https scheme', function (string $url) {
    artisan('bladecn:add-registry')
        ->expectsQuestion('Please enter your BladeCN registry URL:', $url)
        ->expectsOutputToContain('The URL must use HTTPS scheme.')
        ->assertFailed();
})->with(['http://github.com/blade-cn/registry-example']);

it('should validate the prompt input', function (string $url) {
    artisan('bladecn:add-registry')
        ->expectsQuestion('Please enter your BladeCN registry URL:', $url)
        ->expectsOutputToContain('The URL is invalid.')
        ->assertFailed();
})->with(['test', 'test.com', 'http:/test.com']);

it('should show the error if the file is not found', function () {
    Http::fake([
        'https://raw.githubusercontent.com/blade-cn/registry-example/main/bladecn-registry.json' => Http::response(null, 404),
    ]);

    artisan('bladecn:add-registry')
        ->expectsQuestion('Please enter your BladeCN registry URL:', 'https://github.com/blade-cn/registry-example')
        ->expectsOutputToContain('The registry file was not found at the specified URL [https://github.com/blade-cn/registry-example].')
        ->assertExitCode(Command::FAILURE);
});

it('should show the error if the response has a client error', function () {
    Http::fake([
        'https://raw.githubusercontent.com/blade-cn/registry-example/main/bladecn-registry.json' => Http::response(null, 401),
    ]);

    artisan('bladecn:add-registry')
        ->expectsQuestion('Please enter your BladeCN registry URL:', 'https://github.com/blade-cn/registry-example')
        ->expectsOutputToContain('The request to the registry resulted in a client error. Please check the URL and permissions.')
        ->assertExitCode(Command::FAILURE);
});

it('should show the error if the response has a server error', function () {
    Http::fake([
        'https://raw.githubusercontent.com/blade-cn/registry-example/main/bladecn-registry.json' => Http::response(null, 500),
    ]);

    artisan('bladecn:add-registry')
        ->expectsQuestion('Please enter your BladeCN registry URL:', 'https://github.com/blade-cn/registry-example')
        ->expectsOutputToContain('The registry server is experiencing issues. Please try again later.')
        ->assertExitCode(Command::FAILURE);
});

it('should show the error if the file is not a valid json', function () {
    Http::fake([
        'https://raw.githubusercontent.com/blade-cn/registry-example/main/bladecn-registry.json' => Http::response([], 200),
    ]);

    artisan('bladecn:add-registry')
        ->expectsQuestion('Please enter your BladeCN registry URL:', 'https://github.com/blade-cn/registry-example')
        ->expectsOutputToContain('The registry configuration file is invalid or malformed:')
        ->assertExitCode(Command::FAILURE);
});

it('should show the error if the registry already exists', function () {
    Http::fake([
        'https://raw.githubusercontent.com/blade-cn/registry-example/main/bladecn-registry.json' => Http::response([
            'name' => 'Sample Registry',
            'url' => 'https://github.com/blade-cn/registry-example',
            'description' => 'A sample registry',
            'version' => '1.0.0',
            'lastUpdated' => now()->toDateTimeString(),
        ], 200),
    ]);

    $config = RegistryConfig::make();

    $config->persist(RemoteRegistry::from([
        'name' => 'Sample Registry',
        'url' => 'https://github.com/blade-cn/registry-example',
        'description' => 'A sample registry',
        'version' => '1.0.0',
        'lastUpdated' => now()->toIso8601String(),
    ]));

    artisan('bladecn:add-registry')
        ->expectsQuestion('Please enter your BladeCN registry URL:', 'https://github.com/blade-cn/registry-example')
        ->expectsOutputToContain("The remote URL 'https://github.com/blade-cn/registry-example' already exists in the registry.")
        ->assertExitCode(Command::FAILURE);

    expect($config->registries)->toHaveCount(1);
});
