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

final class GitlabClient implements RegistryClient
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
            accessToken: config('bladecn-laravel.source.gitlab.token'),
        );
    }

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

    public function fetchFile(Repo $repo, string $path): string
    {
        $response = $this->httpClient->get($this->buildUrl($repo, $path));

        if ($response->failed()) {
            throw new RequestException($response);
        }

        return $response->body();
    }

    public function buildUrl(Repo $repo, string $path): string
    {
        $branch = config('bladecn-laravel.source.gitlab.branch', 'main');

        return "https://gitlab.com/{$repo->owner}/{$repo->repo}/-/raw/{$branch}";
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

    public function baseUrl(): string
    {
        return 'https://gitlab.com';
    }
}
