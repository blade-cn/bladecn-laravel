<?php

declare(strict_types=1);

namespace Bladecn;

use Bladecn\Exceptions\AlreadyExistsRemoteUrlException;
use Bladecn\Support\ProjectPath;
use Bladecn\ValueObjects\Config;

final class RegistryConfig
{
    public const string JSON_REGISTRY_CONFIG_NAME = 'bladecn-registry.json';

    protected static ?self $instance = null;

    public function __construct(public ?Config $config = null)
    {
        if (is_null($config)) {
            $this->config = new Config;
        }
    }

    public static function make(): self
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }

        if (! self::exists()) {
            return self::$instance = new self;
        }

        $jsonAsArray = json_decode(file_get_contents(self::path()), true, JSON_THROW_ON_ERROR);

        return self::$instance = new self(
            Config::from($jsonAsArray)
        );
    }

    public static function exists(): bool
    {
        return file_exists(self::path());
    }

    public static function path(): string
    {
        return sprintf('%s/%s', ProjectPath::get(), self::JSON_REGISTRY_CONFIG_NAME);
    }

    public static function flush(): void
    {
        self::$instance = null;
    }

    public static function init(): bool
    {
        if (self::exists()) {
            return false;
        }

        return (bool) file_put_contents(
            self::path(),
            json_encode((new Config)->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    public function persist(string $registryUrl): void
    {
        if (! self::exists()) {
            self::init();
        }

        if (in_array($registryUrl, $this->config->registries, true)) {
            throw new AlreadyExistsRemoteUrlException($registryUrl);
        }

        $registries = $this->config->registries;
        $registries[] = $registryUrl;

        $this->config = new Config($registries);

        file_put_contents(self::path(), json_encode($this->config->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
