<?php

namespace App\Models\Constants;

class OrderConstants
{
    public const BUY_TYPE = "buy";
    public const SELL_TYPE = "sell";
    public const  SELL_ORDERS_KEY = 'sellOrders';
    public const  BUY_ORDERS_KEY = 'buyOrders';
    public const  PENDING = 'pending';
    public const  MATCHED = 'matched';
    public const  CANCELLED = 'cancelled';

    public const ORDER_QUEUE_NAME = 'order_queue';
}
