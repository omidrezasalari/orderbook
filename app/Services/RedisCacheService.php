<?php

namespace App\Services;

use App\Contracts\CacheInterface;
use Predis\Client as RedisClient;


class RedisCacheService implements CacheInterface
{
    private RedisClient $redis;

    public function __construct()
    {
        $config = config('services.redis');

        $this->redis = new RedisClient([
            'scheme' => 'tcp',
            'host'   => $config['host'],
            'port'   => $config['port'],
            'password' => $config['password'],
            'database' => $config['database'],
        ]);

    }

    public function setnx(string $key, string $value): bool
    {
        return (bool) $this->redis->setnx($key, $value);
    }

    public function expire(string $key, int $ttl): bool
    {
        return (bool) $this->redis->expire($key, $ttl);
    }

    public function del(string $key): bool
    {
        return (bool) $this->redis->del($key);
    }

    public function zadd(string $key, float $score, string $value): bool
    {
        return (bool) $this->redis->zadd($key, [$score => $value]);
    }

    public function zrange(string $key, int $start, int $end, bool $withscores = false): array
    {
        return $this->redis->zrange($key, $start, $end, $withscores ? 'WITHSCORES' : '');
    }

    public function zrevrange(string $key, int $start, int $end, bool $withscores = false): array
    {
        return $this->redis->zrevrange($key, $start, $end, $withscores ? 'WITHSCORES' : '');
    }

    public function zcard(string $key): int
    {
        return $this->redis->zcard($key);
    }

    public function zrem(string $key, string $member): bool
    {
        return $this->redis->zrem($key, $member);
    }
}

