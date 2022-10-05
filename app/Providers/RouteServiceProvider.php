<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapAdminRoutes();

        $this->mapMerchantOwnerRoutes();

        $this->mapMerchantRoutes();
        
        $this->mapWebRoutes();

    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::domain('api.'.env('APP_URL'))
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

    /**
     * Define the "admin" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapAdminRoutes()
    {
        Route::domain('admin.'.env('APP_URL'))
             ->middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/admin.php'));
    }

    /**
     * Define the "merchant" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapMerchantOwnerRoutes()
    {
        Route::domain('owner.'.env('APP_URL'))
             ->middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/owner.php'));
    }

    /**
     * Define the "merchant" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapMerchantRoutes()
    {
        Route::domain('merchant.'.env('APP_URL'))
             ->middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/merchant.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }
}
