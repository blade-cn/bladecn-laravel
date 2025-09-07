<?php

declare(strict_types=1);

namespace Bladecn\Actions;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class RegistryClient
{
    public function __invoke(string $url, ?string $accessToken = null): PendingRequest
    {
        $client = Http::baseUrl($url)
            ->timeout(5)
            ->retry(3, 100)
            ->withToken($accessToken)
            ->withHeader('Accept', 'application/json');

        if (! is_null($accessToken)) {
            $client->withToken($accessToken);
        }

        return $client;
    }

    public static function make(string $url, ?string $accessToken = null): PendingRequest
    {
        return (new self)($url, $accessToken);
    }
}
