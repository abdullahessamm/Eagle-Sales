<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::domain('api.' . env('APP_URL'))
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api/index.php'));

            Route::domain('suppliers.' . env('APP_URL'))
                ->middleware('availableCountry')
                ->group(function () {
                    Route::view('/{params?}' , 'supplier-dashboard')
                    ->where('params', '.*');
                });

            Route::domain('customers.' . env('APP_URL'))
            ->middleware('availableCountry')
            ->group(function () {
                Route::view('/{params?}' , 'customer-dashboard')
                ->where('params', '.*');
            });

            Route::domain('admins.' . env('APP_URL'))
            ->group(function () {
                Route::view('/{params?}' , 'admin-dashboard')
                ->where('params', '.*');
            });

            Route::domain('store.' . env('APP_URL'))
                ->middleware('availableCountry')
                ->group(function () {
                    Route::view('/{params?}' , 'store')
                    ->where('params', '.*');
                });

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(200)->by($request->user()?->id ?: $request->ip());
        });
    }
}
