<?php

namespace App\Contracts;

interface CacheInterface
{
    public function setnx(string $key, string $value): bool;

    public function expire(string $key, int $seconds): void;

    public function del(string $key): void;
}
