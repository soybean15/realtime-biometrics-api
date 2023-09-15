<?php

namespace App\Providers;

use App\Console\Commands\CheckAttendance;
use App\Http\Controllers\ZkTecoController;
use App\Http\Services\ZkTecoService;
use Illuminate\Support\ServiceProvider;

class ZkTekoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //

        $this->app->bind(ZkTecoService::class, function ($app) {
            return new ZkTecoService();
        });

        // Bind EmployeeService to the container
        $this->app->bind(CheckAttendance::class, function ($app) {
            return new CheckAttendance($app->make(ZkTecoService::class));
        });

        // $this->app->bind(ZkTecoController::class, function ($app) {
        //     return new ZkTecoController($app->make(ZkTecoService::class));
        // });

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
