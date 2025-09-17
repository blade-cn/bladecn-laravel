<?php

declare(strict_types=1);

namespace Bladecn\Contracts;

use Bladecn\ValueObjects\RemoteRegistry;
use Bladecn\ValueObjects\Repo;

interface RegistryClient
{
    public function fetchRegistry(Repo $repo): RemoteRegistry;

    public function fetchFile(Repo $repo, string $path): string;

    public function checkIsAvailable(Repo $repo): bool;

    public function buildUrl(Repo $repo, string $path): string;

    public function baseUrl(): string;
}
