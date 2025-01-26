<?php

namespace App\Models\Constants;

class OrderLockConfig
{
    private const STATUS_LOCKED = 'locked';
   private const KEY_PREFIX = 'order_lock_';
    private const TTL = 4;


    public static function getLockKey(int $identifier): string
    {
        return self::KEY_PREFIX . $identifier;
    }

    public static function getLockStatus(): string
    {
        return self::STATUS_LOCKED;
    }

    public static function getTtl(): int
    {
        return self::TTL;
    }

}
