<?php

// app/Events/OrderMatched.php

namespace App\Events;

use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderMatched
{
    use Dispatchable, SerializesModels;

    public Order $buyOrder;
    public Order $sellOrder;

    public function __construct(Order $buyOrder, Order $sellOrder)
    {
        $this->buyOrder = $buyOrder;
        $this->sellOrder = $sellOrder;
    }
}
