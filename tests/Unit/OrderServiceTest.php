<?php

namespace Tests\Unit\Services;

use App\Models\Constants\OrderConstants;
use App\Models\Constants\OrderLockConfig;
use App\Services\OrderService;
use App\Models\Order;
use App\DTOs\PlaceAnOrder;
use App\Repositories\OrderRepositoryInterface;
use App\Contracts\CacheInterface;
use App\Contracts\MessageBrokerInterface;
use PHPUnit\Framework\TestCase;
use Mockery;

class OrderServiceTest extends TestCase
{
    private OrderService $orderService;
    private $orderRepositoryMock;
    private $cacheServiceMock;
    private $messageBrokerMock;

    protected function setUp(): void
    {
        $this->orderRepositoryMock = Mockery::mock(OrderRepositoryInterface::class);
        $this->cacheServiceMock = Mockery::mock(CacheInterface::class);
        $this->messageBrokerMock = Mockery::mock(MessageBrokerInterface::class);

        $this->orderService = new OrderService(
            $this->orderRepositoryMock,
            $this->cacheServiceMock,
            $this->messageBrokerMock
        );
    }

    public function testPlaceAnOrder(): void
    {
        $command = new PlaceAnOrder(OrderConstants::BUY_TYPE, 100, 10);
        $order = new Order(['type' => OrderConstants::BUY_TYPE, 'price' => 100, 'quantity' => 10]);

        $this->orderRepositoryMock->shouldReceive('create')->once()
            ->with([
                'type' => OrderConstants::BUY_TYPE,
                'price' => 100,
                'quantity' => 10
            ])->andReturn($order);

        $this->messageBrokerMock
            ->shouldReceive('sendMessage')
            ->once()
            ->with(OrderConstants::ORDER_QUEUE_NAME, $order);


        $result = $this->orderService->placeAnOrder($command);


        $this->assertInstanceOf(Order::class, $result);
        $this->assertEquals(OrderConstants::BUY_TYPE, $result->getType());
        $this->assertEquals(100, $result->getPrice());
        $this->assertEquals(10, $result->getQuantity());
    }

    public function testProcessOrders(): void
    {
        $orderData = ['id' => 1, 'type' => OrderConstants::BUY_TYPE, 'price' => 100, 'quantity' => 10];
        $order = Mockery::mock(Order::class);
        $messageMock = Mockery::mock();
        $messageMock->shouldReceive('body')->andReturn(json_encode($orderData));
        $messageMock->shouldReceive('delivery_info')->andReturn(['delivery_tag' => 'tag1']);

        $this->orderRepositoryMock
            ->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($order);

        $this->messageBrokerMock
            ->shouldReceive('consumeMessages')
            ->once()
            ->with(OrderConstants::ORDER_QUEUE_NAME, Mockery::on(function ($callback) {
                $callback(Mockery::mock());
                return true;
            }));

        $this->orderService->processOrders();
    }

    public function testProcessAnOrderWhenLockIsAcquired(): void
    {
        $cachedKey = OrderLockConfig::getLockKey(1);
        $order = Mockery::mock(Order::class);
        $order->shouldReceive('getId')->andReturn(1);
        $this->cacheServiceMock
            ->shouldReceive('setnx')
            ->once()
            ->with($cachedKey, OrderLockConfig::getLockStatus())
            ->andReturn(true);
        $this->cacheServiceMock
            ->shouldReceive('expire')
            ->once()
            ->with($cachedKey, 30);

        $this->cacheServiceMock
            ->shouldReceive('del')
            ->once()
            ->with($cachedKey);

        $this->orderService->processAnOrder($order);
    }

    public function testProcessAnOrderWhenLockCannotBeAcquired(): void
    {
        $cachedKey = OrderLockConfig::getLockKey(1);

        $order = Mockery::mock(Order::class);
        $order->shouldReceive('getId')->andReturn(1);

        $this->cacheServiceMock
            ->shouldReceive('setnx')
            ->once()
            ->with($cachedKey, OrderLockConfig::getLockStatus())
            ->andReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Order is being processed by another instance.");
        $this->orderService->processAnOrder($order);
    }

    /**
     * @throws \Exception
     */
    public function testMatchBuyOrder(): void
    {
        $buyOrder = Mockery::mock(Order::class);
        $buyOrder->shouldReceive('getType')->andReturn(OrderConstants::BUY_TYPE);
        $buyOrder->shouldReceive('getPrice')->andReturn(100);

        $topSellOrder = [
            'id' => 2,
            'price' => 90,
            'quantity' => 5
        ];

        $this->cacheServiceMock
            ->shouldReceive('zcard')
            ->andReturn(1);

        $this->cacheServiceMock
            ->shouldReceive('zrange')
            ->with(OrderConstants::SELL_ORDERS_KEY, 0, 0, true)
            ->andReturn([json_encode($topSellOrder)]);

        $this->orderService->matchBuyOrder($buyOrder);

        $this->cacheServiceMock->shouldHaveReceived('zadd')->once();
    }
}
