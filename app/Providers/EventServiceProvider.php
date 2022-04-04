<?php

namespace App\Providers;

use App\Events\Accounts\UserHasBeenBanned;
use App\Events\Accounts\UserHasBeenReactivated;
use App\Events\Items\ItemApprovalResponse;
use App\Listeners\Accounts\DeactivateItemsForBannedSupplier;
use App\Listeners\Accounts\ReactivateItemsForReactivatedSupplier;
use App\Listeners\Items\SendMailToSupplier;
use App\Listeners\Items\SendSmsToSupplier;
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
        UserHasBeenBanned::class => [
            DeactivateItemsForBannedSupplier::class,
        ],

        UserHasBeenReactivated::class => [
            ReactivateItemsForReactivatedSupplier::class
        ],
        ItemApprovalResponse::class => [
            SendMailToSupplier::class,
            SendSmsToSupplier::class
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
