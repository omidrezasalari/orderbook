<?php

namespace App\Transformers;

use App\Models\Order;

class OrderDataTransformer
{
    public function transform(Order $order): array
    {
        return [
            'id' => $order->id,
            'type' => $order->type,
            'price' => $order->price,
            'quantity' => $order->quantity,
            'status' => $order->status,
            'created_at' => $order->created_at->toDateTimeString(),
            'updated_at' => $order->updated_at->toDateTimeString(),
        ];
    }
}

