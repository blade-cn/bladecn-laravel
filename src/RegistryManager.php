<?php

declare(strict_types=1);

namespace Bladecn;

use Bladecn\Actions\Validations\ValidateConfigData;
use Bladecn\Clients\BitbucketClient;
use Bladecn\Clients\GithubClient;
use Bladecn\Clients\GitlabClient;
use Bladecn\Contracts\RegistryClient;
use Bladecn\Factories\HttpClientFactory;
use Bladecn\Services\Cache;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Manager;

/**
 * @method RegistryClient driver(?string $driver = null)
 */
final class RegistryManager extends Manager
{
    protected int $cacheDuration = 86400;

    public function __construct(
        protected RegistryConfig $registryConfig,
        protected Container $app
    ) {
        parent::__construct($app);
        $this->cacheDuration = (int) $this->app['config']['bladecn-laravel.cache_ttl'];
    }

    public function getDefaultDriver(): ?string
    {
        return null;
    }

    protected function createGithubDriver(): RegistryClient
    {
        return new GithubClient(
            cacheDuration: $this->cacheDuration,
            httpClientFactory: new HttpClientFactory,
            cache: new Cache,
            validateConfigData: new ValidateConfigData,
        );
    }

    protected function createGitlabDriver(): RegistryClient
    {
        return new GitlabClient(
            cacheDuration: $this->cacheDuration,
            httpClientFactory: new HttpClientFactory,
            cache: new Cache,
            validateConfigData: new ValidateConfigData,
        );
    }

    protected function createBitbucketDriver(): RegistryClient
    {
        return new BitbucketClient(
            cacheDuration: $this->cacheDuration,
            httpClientFactory: new HttpClientFactory,
            cache: new Cache,
            validateConfigData: new ValidateConfigData,
        );
    }
}
