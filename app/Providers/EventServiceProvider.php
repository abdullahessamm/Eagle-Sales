<?php

namespace App\Providers;

use App\Events\Accounts\NewUserArrived;
use App\Events\Accounts\UserHasBeenBanned;
use App\Events\Accounts\UserHasBeenReactivated;
use App\Events\Items\ItemApprovalResponse;
use App\Events\Items\ItemRated;
use App\Events\Items\NewItemCreated;
use App\Events\Orders\NewOrderCreated;
use App\Events\Orders\OrderStateChanged;
use App\Events\UserHasBeenApproved;
use App\Listeners\Accounts\DeactivateItemsForBannedSupplier;
use App\Listeners\Accounts\ReactivateItemsForReactivatedSupplier;
use App\Listeners\Items\SendMailToSupplier;
use App\Listeners\Items\SendSmsToSupplier;
use App\Listeners\NotifyUsers;
use App\Listeners\Orders\CalculateCommissions;
use App\Listeners\SendApproveMailNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        NewUserArrived::class => [
            NotifyUsers::class
        ],
        UserHasBeenApproved::class => [
            SendApproveMailNotification::class
        ],
        UserHasBeenBanned::class => [
            DeactivateItemsForBannedSupplier::class,
        ],
        UserHasBeenReactivated::class => [
            ReactivateItemsForReactivatedSupplier::class
        ],
        NewItemCreated::class => [
            NotifyUsers::class
        ],
        ItemApprovalResponse::class => [
            SendMailToSupplier::class,
            SendSmsToSupplier::class,
            NotifyUsers::class
        ],
        ItemRated::class => [
            NotifyUsers::class
        ],
        NewOrderCreated::class => [
            NotifyUsers::class
        ],
        OrderStateChanged::class => [
            NotifyUsers::class,
            CalculateCommissions::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
