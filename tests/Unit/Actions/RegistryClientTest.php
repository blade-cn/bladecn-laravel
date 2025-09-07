<?php

declare(strict_types=1);

use Bladecn\Actions\RegistryClient;
use Bladecn\Enums\SourceControl;
use Bladecn\RegistryConfig;
use Bladecn\ValueObjects\Repo;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->repo = new Repo(SourceControl::GITHUB, 'blade-cn', 'example-registry');
});

it('returns an http client with expected defaults', function () {
    $client = app(RegistryClient::class)(
        $this->repo->rawContentUrl(),
        $this->repo->accessToken()
    );

    expect($client)->toBeInstanceOf(PendingRequest::class);

    $options = $client->getOptions();

    expect($options['timeout'])->toBe(5)
        ->and($options['headers']['Accept'])
        ->toBe('application/json');
});

it('returns an instance when created via make', function () {
    $client = RegistryClient::make(
        $this->repo->rawContentUrl(),
        $this->repo->accessToken()
    );

    expect($client)->toBeInstanceOf(PendingRequest::class);
});

it('sets the Authorization header when a token is provided', function () {
    $token = 'secret-token';
    $url = 'https://example.com/api';
    $client = app(RegistryClient::class)($url, $token);

    $headers = $client->getOptions()['headers'];

    expect($headers)->toHaveKey('Authorization')
        ->and($headers['Authorization'])->toBe("Bearer {$token}");
});

it('retries three times on failure', function () {
    $url = 'https://github.com/blade-cn/example-registry';
    $token = 'abc123';

    $hitCount = 0;

    Http::fake([
        $url . '*' => function () use (&$hitCount) {
            $hitCount++;

            return Http::response('server error', 500);
        },
    ]);

    $client = app(RegistryClient::class)($url, $token);
    try {
        $client->get(RegistryConfig::JSON_REGISTRY_CONFIG_NAME);
    } catch (Throwable $e) {
        //
    }

    expect($hitCount)->toBe(3);
});
