<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void{
        // $schedule->command('inspire')->hourly();
        $schedule->command('urpp:job')->dailyAt('00:00');
        $schedule->command('app:test')->dailyAt('00:00');
        $schedule->command('DayOfArrivalEmailCommandJob')->dailyAt('14:00');
        $schedule->command('DayOfDepartureEmailCommandJob')->dailyAt('14:00');
        $schedule->command('CreateInvoiceEmailCommandJob')->dailyAt('14:00');
        $schedule->command('ChangeRuCurrencyCommand')->dailyAt('14:00');
        $schedule->command('job:getRuBookings')->dailyAt('14:00');
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