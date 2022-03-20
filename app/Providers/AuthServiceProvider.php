<?php

namespace App\Providers;

use App\Auth\CacheTokenProvider;
use App\Models\BackOfficeUser;
use App\Models\Customer;
use App\Models\CustomerCategory;
use App\Models\Seller;
use App\Models\Supplier;
use App\Policies\BackOfficeUserPolicy;
use App\Policies\CustomerCategoryPloicy;
use App\Policies\CustomerPolicy;
use App\Policies\SellerPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        BackOfficeUser::class   => BackOfficeUserPolicy::class,
        Supplier::class         => SupplierPolicy::class,
        Seller::class           => SellerPolicy::class,
        Customer::class         => CustomerPolicy::class,
        CustomerCategory::class => CustomerCategoryPloicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Auth::provider('cache-token', function () {
            return resolve(CacheTokenProvider::class);
        });
        //
    }
}
