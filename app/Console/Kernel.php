<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\ProcessPayrollCommand::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Run payroll processing check daily at midnight
        $schedule->command('payroll:process')
                ->dailyAt('00:00')
                ->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}