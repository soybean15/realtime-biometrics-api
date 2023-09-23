<?php

namespace App\Console;

use App\Models\Setting;
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
        $settings = Setting::find(1);
        $enable = $settings->data['live_update'];
        $schedule->command('get:config')->everySecond();
        if ($enable) {
            $schedule->command('check:attendance')->everySecond();
        }
        $schedule->command('check:attendance')->daily();

       


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