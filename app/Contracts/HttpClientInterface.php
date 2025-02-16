<?php

namespace App\Contracts;

interface HttpClientInterface
{
    public function get(string $url, array $params = []): array;
}
