<?php

declare(strict_types=1);

namespace Bladecn\Clients;

use Bladecn\Actions\Validations\ValidateConfigData;
use Bladecn\Contracts\RegistryClient;
use Bladecn\Factories\HttpClientFactory;
use Bladecn\RegistryConfig;
use Bladecn\Services\Cache;
use Bladecn\ValueObjects\RemoteRegistry;
use Bladecn\ValueObjects\Repo;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Validation\ValidationException;

final class GithubClient implements RegistryClient
{
    protected PendingRequest $httpClient;

    public function __construct(
        protected int $cacheDuration,
        protected HttpClientFactory $httpClientFactory,
        protected Cache $cache,
        protected ValidateConfigData $validateConfigData,
    ) {
        $this->httpClient = $httpClientFactory->build(
            url: $this->baseUrl(),
            accessToken: config('bladecn-laravel.source.github.token'),
        );
    }

    /**
     * @throws RequestException|ValidationException
     */
    public function fetchRegistry(Repo $repo): RemoteRegistry
    {
        $response = $this->httpClient
            ->withHeader('Accept', 'application/json')
            ->get($this->buildUrl($repo, RegistryConfig::JSON_REGISTRY_CONFIG_NAME));

        if ($response->failed()) {
            throw new RequestException($response);
        }

        $data = ($this->validateConfigData)($response->json() ?? [])->validated();

        return RemoteRegistry::from($data);
    }

    /**
     * @throws RequestException
     */
    public function fetchFile(Repo $repo, string $path): string
    {
        $response = $this->httpClient
            ->withHeaders([
                'Accept' => 'application/vnd.github.v3.raw',
                'Cache-Control' => 'no-cache',
            ])
            ->get($this->buildUrl($repo, $path));

        if ($response->failed()) {
            throw new RequestException($response);
        }

        return $response->body();
    }

    public function checkIsAvailable(Repo $repo): bool
    {
        try {
            $response = $this->httpClient
                ->withHeader('Accept', 'application/json')
                ->get($this->buildUrl($repo, RegistryConfig::JSON_REGISTRY_CONFIG_NAME));

            if ($response->failed()) {
                return false;
            }

            return $response->successful();
        } catch (RequestException|ConnectionException) {
            return false;
        }
    }

    public function buildUrl(Repo $repo, string $path): string
    {
        return "{$repo->owner}/{$repo->repo}/main/{$path}";
    }

    public function baseUrl(): string
    {
        return 'https://raw.githubusercontent.com';
    }
}
