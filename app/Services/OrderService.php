<?php

namespace App\Services;

use App\Contracts\CacheInterface;
use App\Contracts\MessageBrokerInterface;
use App\Models\Constants\OrderConstants;
use App\Models\Constants\OrderLockConfig;
use App\Repositories\OrderRepositoryInterface;
use App\Models\Order;
use App\Commands\PlaceAnOrderCommand;
use App\Events\OrderMatchedEvent;

final class OrderService
{

    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly CacheInterface           $cacheService,
        private readonly MessageBrokerInterface   $messageBroker
    ){}

    /**
     * @throws \Exception
     */
    public function placeAnOrder(PlaceAnOrderCommand $command): Order
    {
        $order = $this->orderRepository->create([
            'type' => $command->type(),
            'price' => $command->price(),
            'quantity' => $command->quantity(),
        ]);

        $this->messageBroker->sendMessage(OrderConstants::ORDER_QUEUE_NAME, $order);

        return $order;
    }

    public function processOrders(): void
    {
        $callback = function ($msg) {
            $orderData = json_decode($msg->body, true);

            $order = $this->orderRepository->find($orderData['id']);

            if (!$order) {
                $order = $this->orderRepository->create($orderData);
            }

            $this->processAnOrder($order);


            $this->messageBroker->acknowledgeMessage($msg->delivery_info['delivery_tag']);
        };

        $this->messageBroker->consumeMessages(OrderConstants::ORDER_QUEUE_NAME, $callback);
    }

    /**
     * @throws \Exception
     */
    public function processAnOrder(Order $order): void
    {
        $orderLockKey = OrderLockConfig::getLockKey($order->getId());
        $lockStatus   = OrderLockConfig::getLockStatus();
        $ttl          = OrderLockConfig::getTtl();

        if ($this->cacheService->setnx($orderLockKey, $lockStatus)) {
            $this->cacheService->expire($orderLockKey, $ttl);

            try {
                match ($order->getType()) {
                    OrderConstants::BUY_TYPE => $this->matchBuyOrder($order),
                    OrderConstants::SELL_TYPE => $this->matchSellOrder($order),
                    default => throw new \Exception('Invalid order type'),
                };
            } finally {
                $this->cacheService->del($orderLockKey);
            }
        } else {
            //TODO we must config Exception Handler for Customize error handling.
            throw new \Exception("Order is being processed by another instance.");
        }
    }

    /**
     * @throws \Exception
     */
    private function matchBuyOrder(Order $buyOrder): void
    {
        while ($this->hasSellOrders()) {
            $topSellOrder = $this->getTopSellOrder();

            if ($this->canMatchOrders($topSellOrder, $buyOrder)) {
                $this->processMatchedOrder($topSellOrder, $buyOrder);
                break;
            } else {
                break;
            }
        }

        $this->addBuyOrderToCacheIfNoMatch($buyOrder);
    }

    private function matchSellOrder(Order $sellOrder): void
    {
        while ($this->hasBuyOrders()) {
            $topBuyOrder = $this->getTopBuyOrder();

            if ($this->canMatchOrders($topBuyOrder, $sellOrder)) {
                $this->processMatchedOrder($topBuyOrder, $sellOrder);
                break;
            } else {
                break;
            }
        }

        $this->addSellOrderToCacheIfNoMatch($sellOrder);
    }

    private function hasSellOrders(): bool
    {
        return $this->cacheService->zcard(OrderConstants::SELL_ORDERS_KEY) > 0;
    }

    private function hasBuyOrders(): bool
    {
        return $this->cacheService->zcard(OrderConstants::BUY_ORDERS_KEY) > 0;
    }

    private function getTopSellOrder(): array
    {
        $topSellOrder = $this->cacheService->zrange(OrderConstants::SELL_ORDERS_KEY, 0, 0, true);
        return json_decode($topSellOrder[0], true);
    }

    private function getTopBuyOrder(): array
    {
        $topBuyOrder = $this->cacheService->zrevrange(OrderConstants::BUY_ORDERS_KEY, 0, 0, true);
        return json_decode($topBuyOrder[0], true);
    }

    private function canMatchOrders(array $topOrder, Order $currentOrder): bool
    {
        if ($currentOrder->getType() === OrderConstants::BUY_TYPE) {
            return $topOrder['price'] <= $currentOrder->getPrice();
        }

        return $topOrder['price'] >= $currentOrder->getPrice();
    }

    private function processMatchedOrder(array $topOrder, Order $currentOrder): void
    {
        $order = $this->orderRepository->findById($topOrder['id']);

        if (!$order) {
            throw new \Exception("Matched order not found.");
        }

        $this->updateOrderBookCache($topOrder, $currentOrder, $order);

        event(new OrderMatchedEvent($currentOrder, $order));
    }

    private function updateOrderBookCache(array $topOrder, Order $currentOrder, Order $matchedOrder): void
    {
        $orderTypeToProcessKey = $topOrder['price'] <= $currentOrder->getPrice() ?
            OrderConstants::SELL_ORDERS_KEY : OrderConstants::BUY_ORDERS_KEY;

        $oppositeOrderKey = $topOrder['price'] <= $currentOrder->getPrice() ?
            OrderConstants::BUY_ORDERS_KEY : OrderConstants::SELL_ORDERS_KEY;

        if ($matchedOrder->getQuantity() > $currentOrder->getQuantity()) {
            $this->cacheService->zadd($orderTypeToProcessKey, $matchedOrder->getPrice(), json_encode($matchedOrder));
        }
        elseif ($currentOrder->getQuantity() > $matchedOrder->getQuantity()) {
            $this->cacheService->zadd($oppositeOrderKey, $currentOrder->getPrice(), json_encode($currentOrder));
        }
    }

    private function addBuyOrderToCacheIfNoMatch(Order $buyOrder): void
    {
        if ($this->hasSellOrders() && $this->getTopSellOrder()['price'] > $buyOrder->getPrice()) {
            $this->cacheService->zadd(OrderConstants::BUY_ORDERS_KEY, $buyOrder->getPrice(), json_encode($buyOrder));
        }
    }

    private function addSellOrderToCacheIfNoMatch(Order $sellOrder): void
    {
        if ($this->hasBuyOrders() && $this->getTopBuyOrder()['price'] < $sellOrder->getPrice()) {
            $this->cacheService->zadd(OrderConstants::SELL_ORDERS_KEY, $sellOrder->getPrice(), json_encode($sellOrder));
        }
    }

}

