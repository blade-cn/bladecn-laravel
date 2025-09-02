<?php

declare(strict_types=1);

namespace Bladecn;

use Bladecn\Exceptions\AlreadyExistsRemoteUrlException;
use Bladecn\Support\ProjectPath;
use Bladecn\ValueObjects\RegistrySchema;
use Bladecn\ValueObjects\RemoteRegistry;

final class RegistryConfig
{
    public const JSON_REGISTRY_CONFIG_NAME = 'bladecn-registry.json';

    protected static ?self $instance = null;

    /** @param array<RegistrySchema> $registries */
    public function __construct(public array $registries = [])
    {
        //
    }

    public static function make(): self
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }

        $contents = file_exists(self::path())
         ? (string) file_get_contents(self::path())
            : '{"registries":[]}';

        $jsonAsArray = json_decode($contents, true, JSON_THROW_ON_ERROR);

        return self::$instance = new self(
            registries: array_map(
                fn (array $registry) => RemoteRegistry::from($registry),
                $jsonAsArray['registries'] ?? []
            )
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
            json_encode([
                'registries' => [],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    public function persist(RemoteRegistry $registry): void
    {
        if (! self::exists()) {
            self::init();
        }

        $urls = array_map(fn (RemoteRegistry $r) => $r->url, $this->registries);

        if (in_array($registry->url, $urls, true)) {
            throw new AlreadyExistsRemoteUrlException($registry->url);
        }

        $this->registries[] = $registry;

        file_put_contents(self::path(), json_encode([
            'registries' => array_merge($this->registries, [$registry]),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
