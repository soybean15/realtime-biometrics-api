<?php

namespace App\Console;


use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */


    protected $commands = [
        Commands\CheckAttendance::class,

    ];
    protected function schedule(Schedule $schedule): void
    {
 
       // $schedule->command('generate:report')->dailyAt('22:00');
        // $schedule->command('get:config')->everySecond();
      
        // $schedule->command('check:attendance')->everySecond();
        
     
       


    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}