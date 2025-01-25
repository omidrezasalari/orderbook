<?php
namespace App\Interfaces;

use App\Models\Order;

interface OrderMatchingInterface
{
    public function processOrder(Order $order): void;
}
