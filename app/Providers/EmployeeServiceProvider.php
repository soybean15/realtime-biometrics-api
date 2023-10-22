<?php

namespace App\Providers;

use App\Actions\Employee\DeleteEmployee;
use App\Http\Managers\EmployeeManager;
use App\Http\Managers\ReportManager;
use Illuminate\Support\ServiceProvider;
use App\Actions\Employee\CreateNewEmployee;
use Illuminate\Contracts\Foundation\Application;
class EmployeeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind CreateNewEmployee to the container
        $this->app->bind(CreateNewEmployee::class, function ($app) {
            return new CreateNewEmployee();
        });

        $this->app->bind(DeleteEmployee::class, function ($app) {
            return new DeleteEmployee();
        });

        // Bind EmployeeService to the container
        $this->app->bind(EmployeeManager::class, function ($app) {
            return new EmployeeManager($app->make(CreateNewEmployee::class),$app->make(DeleteEmployee::class));
        });

        $this->app->singleton(ReportManager::class, function (Application $app) {
            return new ReportManager();
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
