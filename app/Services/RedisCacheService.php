<?php

namespace App\Services;

use App\Contracts\CacheInterface;
use Illuminate\Support\Facades\Redis;

class RedisCacheService implements CacheInterface
{
    public function setnx(string $key, string $value): bool
    {
        return Redis::setnx($key, $value);
    }

    public function expire(string $key, int $seconds): void
    {
        Redis::expire($key, $seconds);
    }

    public function del(string $key): void
    {
        Redis::del($key);
    }
}

