<?php

namespace App\Services;

use App\Events\OrderMatched;
use App\Models\Order;
use App\Interfaces\OrderMatchingInterface;
use Illuminate\Support\Facades\Redis;

class OrderMatchingService implements OrderMatchingInterface
{
    protected array $buyOrders = [];
    protected array $sellOrders = [];
    protected $orderRepository;

    public function __construct($orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function processOrder(Order $order): void
    {
        $orderLockKey = "order_lock_{$order->id}";

        if (Redis::setnx($orderLockKey, 'locked')) {
            Redis::expire($orderLockKey, 10);

            try {
                if ($order->type === 'buy') {
                    $this->matchBuyOrder($order);
                } else {
                    $this->matchSellOrder($order);
                }
            } finally {
                Redis::del($orderLockKey);
            }
        } else {
            throw new \Exception("Order is being processed by another instance.");
        }
    }

    private function matchBuyOrder(Order $buyOrder): void
    {
        while (!empty($this->sellOrders) && $this->sellOrders[0]->price <= $buyOrder->price) {
            $sellOrder = array_shift($this->sellOrders);

            event(new OrderMatched($buyOrder, $sellOrder));

            if ($sellOrder->quantity > $buyOrder->quantity) {
                $this->sellOrders[] = $sellOrder;
            } elseif ($buyOrder->quantity > $sellOrder->quantity) {
                $this->buyOrders[] = $buyOrder;
            }
            break;
        }

        if (empty($this->sellOrders) || $this->sellOrders[0]->price > $buyOrder->price) {
            $this->buyOrders[] = $buyOrder;
            usort($this->buyOrders, fn($a, $b) => $b->price <=> $a->price); // مرتب‌سازی Max Heap
        }
    }

    private function matchSellOrder(Order $sellOrder): void
    {
        while (!empty($this->buyOrders) && $this->buyOrders[0]->price >= $sellOrder->price) {
            $buyOrder = array_shift($this->buyOrders);

            event(new OrderMatched($sellOrder, $buyOrder));

            if ($sellOrder->quantity > $buyOrder->quantity) {
                $this->buyOrders[] = $buyOrder;
            } elseif ($buyOrder->quantity > $sellOrder->quantity) {
                $this->sellOrders[] = $sellOrder;
            }
            break;
        }

        if (empty($this->buyOrders) || $this->buyOrders[0]->price < $sellOrder->price) {
            $this->sellOrders[] = $sellOrder;
            usort($this->sellOrders, fn($a, $b) => $a->price <=> $b->price);
        }
    }
}
