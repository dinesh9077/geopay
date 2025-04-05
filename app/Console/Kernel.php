<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('transaction:update-onafric-collection-status')->everyMinute(); // Corrected method name
        $schedule->command('transaction:update-lightnet-status')->everyTwoMinutes(); // Corrected method name
		//$schedule->command('transaction:update-onafric-status')->everyTenMinutes(); // Corrected method name
		$schedule->command('fetch:lightnet-exchange-rates')->hourly(); 
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
