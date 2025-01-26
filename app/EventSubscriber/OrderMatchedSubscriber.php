<?php

namespace App\EventSubscriber;

namespace App\EventSubscriber;

use App\Contracts\CacheInterface;
use App\Events\OrderMatchedEvent;
use App\Models\Constants\OrderConstants;
use App\Repositories\OrderRepositoryInterface;
use App\Services\NotificationService;

class OrderMatchedSubscriber
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly CacheInterface           $cacheService,
        private readonly NotificationService      $notificationService
    ){}

    public function handle(OrderMatchedEvent $event): void
    {
        $buyOrder = $event->buyOrder();
        $sellOrder = $event->sellOrder();

        $orders = $this->orderRepository->findByIds([$buyOrder->getId(),$sellOrder->getId()]);

        array_walk($orders, function ($order) {
            $this->orderRepository->update($order, ['status' => OrderConstants::MATCHED]);

            $orderKey = $order['type'] === OrderConstants::BUY_TYPE ?
                OrderConstants::BUY_ORDERS_KEY : OrderConstants::SELL_ORDERS_KEY;

            $this->cacheService->zrem($orderKey, json_encode($order));
        });

        $this->sendNotification($buyOrder, $sellOrder);
    }

    private function sendNotification($buyOrder, $sellOrder): void
    {
        // $buyOrder->user()->phoneNumber()
        // $sellOrder->user()->phoneNumber()

        $sellerPhoneNumber = '0912*******';
        $buyPhoneNumber = '0912*******';

        $this->notificationService->sendOrderMatchedSms($sellerPhoneNumber, $buyPhoneNumber);
    }
}

