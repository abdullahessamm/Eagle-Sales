<?php

namespace App\Providers;

use App\Auth\CacheTokenProvider;
use App\Models\AvailableCity;
use App\Models\AvailableCountry;
use App\Models\BackOfficeUser;
use App\Models\Customer;
use App\Models\CustomerCategory;
use App\Models\DuesOfSeller;
use App\Models\Item;
use App\Models\JourneyPlan;
use App\Models\Order;
use App\Models\OurCommission;
use App\Models\Seller;
use App\Models\Supplier;
use App\Policies\AvailableCityPolicy;
use App\Policies\AvailableCountryPolicy;
use App\Policies\BackOfficeUserPolicy;
use App\Policies\CustomerCategoryPloicy;
use App\Policies\CustomerPolicy;
use App\Policies\DuesOfSellerPolicy;
use App\Policies\InventoryCategoryPolicy;
use App\Policies\ItemPolicy;
use App\Policies\JourneyPlanPolicy;
use App\Policies\OrdersPolicy;
use App\Policies\OurCommissionPolicy;
use App\Policies\SellerPolicy;
use App\Policies\SupplierPolicy;
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
        BackOfficeUser::class    => BackOfficeUserPolicy::class,
        Supplier::class          => SupplierPolicy::class,
        Seller::class            => SellerPolicy::class,
        Customer::class          => CustomerPolicy::class,
        CustomerCategory::class  => CustomerCategoryPloicy::class,
        Item::class              => ItemPolicy::class,
        InventoryCategory::class => InventoryCategoryPolicy::class,
        Order::class             => OrdersPolicy::class,
        AvailableCountry::class  => AvailableCountryPolicy::class,
        AvailableCity::class     => AvailableCityPolicy::class,
        OurCommission::class     => OurCommissionPolicy::class,
        DuesOfSeller::class      => DuesOfSellerPolicy::class,
        JourneyPlan::class       => JourneyPlanPolicy::class,
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
