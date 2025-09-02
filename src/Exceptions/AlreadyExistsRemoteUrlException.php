<?php

declare(strict_types=1);

namespace Bladecn\Exceptions;

use RuntimeException;

class AlreadyExistsRemoteUrlException extends RuntimeException
{
    public function __construct(string $url)
    {
        parent::__construct("The remote URL '{$url}' already exists in the registry.");
    }
}
