<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Services\EmployeeService;
use App\Actions\Employee\CreateNewEmployee;

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

        // Bind EmployeeService to the container
        $this->app->bind(EmployeeService::class, function ($app) {
            return new EmployeeService($app->make(CreateNewEmployee::class));
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
