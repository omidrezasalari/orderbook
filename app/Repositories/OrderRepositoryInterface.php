<?php

namespace App\Repositories;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function create(array $data): Order;

    public function getAll(): array;

    public function find(int $id): ?Order;

    public function findByIds(array $ids):array;

    public function update(Order $order, array $mapkeysWithValues): void;
}

