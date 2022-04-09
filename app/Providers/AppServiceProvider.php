<?php

namespace App\Providers;

use App\Models\AttackAttempt;
use App\Models\BackOfficeUser;
use App\Models\BlockedIp;
use App\Models\Permission;
use App\Models\PersonalAccessToken;
use App\Models\Phone;
use App\Models\User;
use App\Observers\AttackObserver;
use App\Observers\BackofficeUserObserver;
use App\Observers\BlockedIpObserver;
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

    private function registerObservers() {
        PersonalAccessToken::observe(TokenObserver::class);
        User::observe(UserObserver::class);
        BackOfficeUser::observe(BackofficeUserObserver::class);
        Permission::observe(PermissionsObserver::class);
        AttackAttempt::observe(AttackObserver::class);
        BlockedIp::observe(BlockedIpObserver::class);
        Phone::observe(PhoneObserver::class);
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
