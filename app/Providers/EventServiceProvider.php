<?php

namespace App\Providers;

use App\Events\MinStockReached;
use App\Listeners\MinStockReachedListener;
use App\Listeners\MinStockReachedMessageBrokerListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        MinStockReached::class => [
            MinStockReachedMessageBrokerListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
