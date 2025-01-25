<?php

namespace App\Services;

use App\Contracts\CacheInterface;
use App\Models\Constants\OrderConstnats;
use App\Models\Constants\OrderLockConfig;
use App\Repositories\OrderRepositoryInterface;
use App\Models\Order;
use App\Commands\PlaceOrderCommand;
use App\Events\OrderMatched;

class OrderService
{
    protected array $buyOrders = [];
    protected array $sellOrders = [];

    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly CacheInterface           $cacheService
    ){}

    /**
     * @throws \Exception
     */
    public function placeAnOrder(PlaceOrderCommand $command): Order
    {
        $order = $this->orderRepository->create([
            'type' => $command->type(),
            'price' => $command->price(),
            'quantity' => $command->quantity(),
        ]);

        $this->processAnOrder($order);

        return $order;
    }

    public function processAnOrder(Order $order): void
    {
        $orderLockKey = OrderLockConfig::getLockKey($order->getId());
        $lockStatus = OrderLockConfig::getLockStatus();
        $ttl = OrderLockConfig::getTtl();


        if ($this->cacheService->setnx($orderLockKey, $lockStatus)) {
            $this->cacheService->expire($orderLockKey, $ttl);

            try {
                if ($order->getType() === OrderConstnats::BUY_TYPE) {
                    $this->matchBuyOrder($order);
                } else {
                    $this->matchSellOrder($order);
                }
            } finally {
                $this->cacheService->del($orderLockKey);
            }
        } else {
            throw new \Exception("Order is being processed by another instance.");
        }
    }

    private function matchBuyOrder(Order $buyOrder): void
    {
        while (!empty($this->sellOrders) && $this->sellOrders[0]->getPrice() <= $buyOrder->getPrice()) {
            $sellOrder = array_shift($this->sellOrders);

            event(new OrderMatched($buyOrder, $sellOrder));

            if ($sellOrder->getQuantity() > $buyOrder->getQuantity()) {
                $this->sellOrders[] = $sellOrder;
            } elseif ($buyOrder->getQuantity() > $sellOrder->getQuantity()) {
                $this->buyOrders[] = $buyOrder;
            }
            break;
        }

        if (empty($this->sellOrders) || $this->sellOrders[0]->getPrice() > $buyOrder->getPrice()) {
            $this->buyOrders[] = $buyOrder;
            usort($this->buyOrders, fn($a, $b) => $b->getPrice() <=> $a->getPrice());
        }
    }

    private function matchSellOrder(Order $sellOrder): void
    {
        while (!empty($this->buyOrders) && $this->buyOrders[0]->getPrice() >= $sellOrder->getPrice()) {
            $buyOrder = array_shift($this->buyOrders);

            event(new OrderMatched($sellOrder, $buyOrder));

            if ($sellOrder->quantity > $buyOrder->quantity) {
                $this->buyOrders[] = $buyOrder;
            } elseif ($buyOrder->getQuantity() > $sellOrder->getQuantity()) {
                $this->sellOrders[] = $sellOrder;
            }
            break;
        }

        if (empty($this->buyOrders) || $this->buyOrders[0]->getPrice() < $sellOrder->getPrice()) {
            $this->sellOrders[] = $sellOrder;
            usort($this->sellOrders, fn($a, $b) => $a->getPrice() <=> $b->getPrice());
        }
    }
}
