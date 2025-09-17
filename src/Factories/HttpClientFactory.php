<?php

declare(strict_types=1);

namespace Bladecn\Factories;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

final class HttpClientFactory
{
    public function build(string $url, ?string $accessToken = null, array $headers = []): PendingRequest
    {
        $client = Http::baseUrl($url)
            ->timeout(5)
            ->retry(3, 100);

        if (! empty($headers)) {
            $client->withHeaders($headers);
        }

        if (! is_null($accessToken)) {
            $client->withToken($accessToken);
        }

        return $client;
    }
}
