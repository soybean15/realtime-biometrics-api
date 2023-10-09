<?php

namespace App\Providers;

use App\Http\Services\HolidayService;
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
        $this->app->singleton(HolidayService::class, function (Application $app) {
            return new HolidayService();
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
