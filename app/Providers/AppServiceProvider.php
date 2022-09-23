<?php

namespace App\Providers;

use App\Models\AttackAttempt;
use App\Models\BackOfficeUser;
use App\Models\BlockedIp;
use App\Models\Item;
use App\Models\Order;
use App\Models\Permission;
use App\Models\PersonalAccessToken;
use App\Models\Phone;
use App\Models\User;
use App\Observers\AttackObserver;
use App\Observers\BackofficeUserObserver;
use App\Observers\BlockedIpObserver;
use App\Observers\ItemObserver;
use App\Observers\OrderObserver;
use App\Observers\PermissionsObserver;
use App\Observers\PhoneObserver;
use App\Observers\TokenObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerObservers();
    }

    protected function registerObservers() {
        AttackAttempt::observe(AttackObserver::class);
        BackOfficeUser::observe(BackofficeUserObserver::class);
        BlockedIp::observe(BlockedIpObserver::class);
        Item::observe(ItemObserver::class);
        Order::observe(OrderObserver::class);
        Permission::observe(PermissionsObserver::class);
        PersonalAccessToken::observe(TokenObserver::class);
        Phone::observe(PhoneObserver::class);
        User::observe(UserObserver::class);
    }
}
