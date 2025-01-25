<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository implements OrderRepositoryInterface
{

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function getAll(): array
    {
        return Order::all()->toArray();
    }


    public function find(int $id): ?Order
    {
        return Order::find($id);
    }
}
