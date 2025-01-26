<?php

namespace App\Transformers;

use App\Models\Order;

class OrderDataTransformer
{
    public function transform(Order $order): array
    {
        return [
            'type' => $order->getType(),
            'price' => $order->getPrice(),
            'quantity' => $order->getQuantity(),
        ];
    }
}

