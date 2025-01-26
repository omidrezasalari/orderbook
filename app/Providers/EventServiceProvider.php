<?php

namespace App\Providers;

use App\Events\OrderMatchedEvent;
use App\EventSubscriber\OrderMatchedSubscriber;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderMatchedEvent::class => [
            OrderMatchedSubscriber::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();

        $this->app['events']->subscribe(OrderMatchedSubscriber::class);

    }
}

