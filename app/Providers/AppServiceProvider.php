<?php

namespace App\Providers;

use App\Contracts\CacheInterface;
use App\Contracts\MessageBrokerInterface;
use App\Repositories\OrderRepository;
use App\Repositories\OrderRepositoryInterface;
use App\Services\RabbitMQAdapter;
use App\Services\RedisCacheService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(MessageBrokerInterface::class,RabbitMQAdapter::class);
        $this->app->bind(CacheInterface::class,RedisCacheService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
