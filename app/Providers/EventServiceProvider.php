<?php

namespace App\Providers;

use App\Events\OrderCancelled;
use App\Events\OrderCreated;
use App\Events\OrderShipped;
use App\Events\OrderStatusChanged;
use App\Events\PaymentProofUploaded;
use App\Events\PaymentVerified;
use App\Listeners\HandleOrderCancelled;
use App\Listeners\HandleOrderCreated;
use App\Listeners\HandlePaymentProofUploaded;
use App\Listeners\HandlePaymentVerified;
use App\Listeners\SendOrderStatusNotification;
use App\Listeners\SendShippingNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        OrderCreated::class => [
            HandleOrderCreated::class,
        ],
        OrderStatusChanged::class => [
            SendOrderStatusNotification::class,
        ],
        OrderShipped::class => [
            SendShippingNotification::class,
        ],
        OrderCancelled::class => [
            HandleOrderCancelled::class,
        ],
        PaymentProofUploaded::class => [
            HandlePaymentProofUploaded::class,
        ],
        PaymentVerified::class => [
            HandlePaymentVerified::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
