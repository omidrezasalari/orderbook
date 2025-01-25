<?php

namespace App\Commands;

class PlaceOrderCommand
{
    private string $type;
    private float $price;
    private int $quantity;

    public function __construct(string $type, float $price, int $quantity)
    {
        $this->type = $type;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public function type(): string
    {
        $this->type;

    }

    public function price(): float
    {
        return $this->price;

    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}

