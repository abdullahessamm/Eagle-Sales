<?php

namespace App\Providers;

use App\Models\AttackAttempt;
use App\Models\BackOfficeUser;
use App\Models\BlockedIp;
use App\Models\Item;
use App\Models\Permission;
use App\Models\PersonalAccessToken;
use App\Models\Phone;
use App\Models\User;
use App\Models\VarItemsAttr;
use App\Models\VarItemsAttrVal;
use App\Observers\AttackObserver;
use App\Observers\BackofficeUserObserver;
use App\Observers\BlockedIpObserver;
use App\Observers\ItemObserver;
use App\Observers\PermissionsObserver;
use App\Observers\PhoneObserver;
use App\Observers\TokenObserver;
use App\Observers\UserObserver;
use App\Observers\VarItemsAttrObserver;
use App\Observers\VarItemsAttrValObserver;
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

    private function registerObservers() {
        AttackAttempt::observe(AttackObserver::class);
        BackOfficeUser::observe(BackofficeUserObserver::class);
        BlockedIp::observe(BlockedIpObserver::class);
        Item::observe(ItemObserver::class);
        Permission::observe(PermissionsObserver::class);
        PersonalAccessToken::observe(TokenObserver::class);
        Phone::observe(PhoneObserver::class);
        User::observe(UserObserver::class);
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
}
