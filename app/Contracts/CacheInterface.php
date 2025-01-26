<?php

namespace App\Contracts;

interface CacheInterface
{
    public function setnx(string $key, string $value): bool;

    public function expire(string $key, int $seconds): bool;

    public function del(string $key): bool;

    public function zadd(string $key, float $score, string $value): bool;
    public function zrange(string $key, int $start, int $end, bool $withscores = false): array;
    public function zrevrange(string $key, int $start, int $end, bool $withscores = false): array;
    public function zcard(string $key): int;

    public function zrem(string $key, string $member): bool;
}
