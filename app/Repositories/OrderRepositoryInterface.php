<?php

namespace App\Repositories;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function create(array $data): Order;
    public function getAll(): array;
    public function find(int $id): ?Order;
}

