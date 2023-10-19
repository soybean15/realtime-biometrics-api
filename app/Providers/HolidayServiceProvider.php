<?php

namespace App\Providers;

use App\Http\Managers\HolidayManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
class HolidayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->app->singleton(HolidayManager::class, function (Application $app) {
            return new HolidayManager();
        });
   
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
