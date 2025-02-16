<?php

namespace App\Providers;

use App\Contracts\CacheInterface;
use App\Contracts\HttpClientInterface;
use App\Contracts\MessageBrokerInterface;
use App\Contracts\Repositories\ArticleRepositoryInterface;
use App\Contracts\RequestMapperInterface;
use App\Repositories\ArticleRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderRepositoryInterface;
use App\Services\AcmeXmlRequestMapper;
use App\Services\HttpClientService;
use App\Services\NewsAggregatorService;
use App\Services\RabbitMQAdapter;
use App\Services\RedisCacheService;
use App\Services\Sources\BBCNewsService;
use App\Services\Sources\GuardianService;
use App\Services\Sources\NewsAPIService;
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
        $this->app->bind(RequestMapperInterface::class, AcmeXmlRequestMapper::class);
        $this->app->bind(ArticleRepositoryInterface::class, ArticleRepository::class);
        $this->app->bind(HttpClientInterface::class, HttpClientService::class);

        $this->app->bind(NewsAggregatorService::class, function ($app) {
            return new NewsAggregatorService(
                $app->make(ArticleRepositoryInterface::class),
                [
                    $app->make(NewsAPIService::class),
                    $app->make(GuardianService::class),
                    $app->make(BBCNewsService::class),
                ]
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
