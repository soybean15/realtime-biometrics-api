<?php

namespace App\Providers;

use App\Http\Managers\DashboardManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //


        $this->app->singleton(DashboardManager::class, function (Application $app) {
            return new DashboardManager();
        });
   
    }
}
