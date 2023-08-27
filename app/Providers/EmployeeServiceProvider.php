<?php

namespace App\Providers;

use App\Actions\Employee\DeleteEmployee;
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

        $this->app->bind(DeleteEmployee::class, function ($app) {
            return new DeleteEmployee();
        });

        // Bind EmployeeService to the container
        $this->app->bind(EmployeeService::class, function ($app) {
            return new EmployeeService($app->make(CreateNewEmployee::class),$app->make(DeleteEmployee::class));
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
