<?php

declare(strict_types=1);

use Bladecn\RegistryConfig;
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
        'https://raw.githubusercontent.com/blade-cn/example-registry/main/bladecn-registry.json' => Http::response([
            'name' => 'BladeCN Registry',
            'homepage' => 'https://blade-cn.dev',
            'description' => 'A registry for BladeCN packages.',
            'authors' => ['Gabor', 'Taylor'],
            'version' => '1.0.0',
            'lastUpdated' => now()->toDateTimeString(),
            'items' => [
                [
                    'name' => 'Hello World',
                    'type' => 'component',
                    'description' => 'A simple hello-world Blade component',
                    'tags' => ['starter', 'example'],
                    'version' => '1.0.0',
                    'files' => [
                        ['path' => 'resources/views/components/hello-world.blade.php', 'type' => 'component'],
                        ['path' => 'resources/css/hello-world.css', 'type' => 'style'],
                    ],
                ],
            ],
        ], 200),
    ]);

    artisan('bladecn:add-registry')
        ->expectsQuestion('Please enter your BladeCN registry URL:', 'https://github.com/blade-cn/example-registry')
        ->expectsConfirmation('Confirm Registry URL: https://github.com/blade-cn/example-registry', 'yes')
        ->expectsOutputToContain('Registry [BladeCN Registry] added successfully.')
        ->assertSuccessful();

    expect(RegistryConfig::make()->config->registries)->toHaveCount(1);
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
        ->expectsOutputToContain('The URL must be a valid URL.')
        ->assertFailed();
})->with(['http://github.com/blade-cn/example-registry']);

it('should validate the prompt input', function (string $url) {
    artisan('bladecn:add-registry')
        ->expectsQuestion('Please enter your BladeCN registry URL:', $url)
        ->expectsOutputToContain('The URL must be a valid URL.')
        ->assertFailed();
})->with(['test', 'test.com', 'http:/test.com']);

it('should show the error if the file is not found', function () {
    Http::fake([
        'https://raw.githubusercontent.com/blade-cn/example-registry/main/bladecn-registry.json' => Http::response(null, 404),
    ]);

    artisan('bladecn:add-registry')
        ->expectsQuestion('Please enter your BladeCN registry URL:', 'https://github.com/blade-cn/example-registry')
        ->expectsOutputToContain('The registry file was not found at the specified URL [https://github.com/blade-cn/example-registry].')
        ->assertExitCode(Command::FAILURE);
});

it('should show the error if the response has a client error', function () {
    Http::fake([
        'https://raw.githubusercontent.com/blade-cn/example-registry/main/bladecn-registry.json' => Http::response(null, 401),
    ]);

    artisan('bladecn:add-registry')
        ->expectsQuestion('Please enter your BladeCN registry URL:', 'https://github.com/blade-cn/example-registry')
        ->expectsOutputToContain('The request to the registry resulted in a client error. Please check the URL and permissions.')
        ->assertExitCode(Command::FAILURE);
});

it('should show the error if the response has a server error', function () {
    Http::fake([
        'https://raw.githubusercontent.com/blade-cn/example-registry/main/bladecn-registry.json' => Http::response(null, 500),
    ]);

    artisan('bladecn:add-registry')
        ->expectsQuestion('Please enter your BladeCN registry URL:', 'https://github.com/blade-cn/example-registry')
        ->expectsOutputToContain('The registry server is experiencing issues. Please try again later.')
        ->assertExitCode(Command::FAILURE);
});

it('should show the error if the file is not a valid json', function () {
    Http::fake([
        'https://raw.githubusercontent.com/blade-cn/example-registry/main/bladecn-registry.json' => Http::response([], 200),
    ]);

    artisan('bladecn:add-registry')
        ->expectsQuestion('Please enter your BladeCN registry URL:', 'https://github.com/blade-cn/example-registry')
        ->expectsOutputToContain('The registry configuration file is invalid or malformed:')
        ->assertExitCode(Command::FAILURE);
});

it('should show the error if the registry already exists', function () {
    $registryConfig = RegistryConfig::make();
    $registryConfig->persist('https://github.com/blade-cn/example-registry');

    artisan('bladecn:add-registry')
        ->expectsQuestion('Please enter your BladeCN registry URL:', 'https://github.com/blade-cn/example-registry')
        ->expectsOutputToContain("The remote URL 'https://github.com/blade-cn/example-registry' already exists in the registry.")
        ->assertExitCode(Command::FAILURE);

    expect($registryConfig->config->registries)->toHaveCount(1);
});

it('should validate the registry url from artisan argument', function () {
    artisan('bladecn:add-registry', ['url' => 'https://google.com'])
        ->expectsOutputToContain('The URL must be from a supported source control platform (GitHub, GitLab, Bitbucket).')
        ->assertFailed();
});
