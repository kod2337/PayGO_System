<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PayrollPeriod;
use Carbon\Carbon;

class ProcessPayrollCommand extends Command
{
    protected $signature = 'payroll:process';
    protected $description = 'Process payroll for periods that are due';

    public function handle()
    {
        $pendingPeriods = PayrollPeriod::where('status', 'pending')
            ->where('next_schedule', '<=', now())
            ->where('is_automated', true)
            ->get();

        if ($pendingPeriods->isEmpty()) {
            $this->info('No payroll periods ready for processing.');
            return;
        }

        foreach ($pendingPeriods as $period) {
            $this->info("Processing payroll period: {$period->start_date} to {$period->end_date}");
            
            try {
                app(\App\Http\Controllers\PayrollController::class)->processPayroll(
                    new \Illuminate\Http\Request(['period_id' => $period->id])
                );
                
                $this->info('Successfully processed payroll period.');
            } catch (\Exception $e) {
                $this->error("Failed to process payroll period: {$e->getMessage()}");
                continue;
            }
        }
    }
}