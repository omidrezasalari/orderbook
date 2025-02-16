<?php

namespace App\Services;

use App\Contracts\HttpClientInterface;
use Illuminate\Support\Facades\Http;

class HttpClientService implements HttpClientInterface
{
    public function get(string $url, array $params = []): array
    {
        $response = Http::get($url, $params);

        if ($response->failed()) {
            throw new \Exception('HTTP request failed: ' . $response->body());
        }

        return $response->json();
    }
}
