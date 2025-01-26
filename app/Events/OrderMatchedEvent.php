<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderMatchedEvent
{
    use Dispatchable, SerializesModels;

    private Order $buyOrder;
    private Order $sellOrder;

    public function __construct(Order $buyOrder, Order $sellOrder)
    {
        $this->buyOrder = $buyOrder;
        $this->sellOrder = $sellOrder;
    }

    public function buyOrder(): Order
    {
        return $this->buyOrder;
    }

    public function sellOrder(): Order
    {
        return $this->sellOrder;
    }
}
