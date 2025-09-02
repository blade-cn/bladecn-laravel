<?php

declare(strict_types=1);

namespace Bladecn\Exceptions;

use Exception;

class InvalidRemoteUrlException extends Exception
{
    protected $message = 'The provided URL is not supported. Only GitHub, GitLab, and Bitbucket URLs are allowed.';
}
