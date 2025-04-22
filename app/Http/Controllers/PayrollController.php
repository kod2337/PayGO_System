<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PayrollRecord;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class PayrollController extends Controller
{
    private function getPayrollPeriods()
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        return collect(range(1, 12))->map(function($month) use ($currentYear, $currentMonth) {
            $date = Carbon::create($currentYear, $month, 1);
            $period = PayrollPeriod::where('start_date', $date->startOfMonth())
                ->first();
            
            return [
                'id' => $period ? $period->id : $month,
                'name' => $date->format('F Y'),
                'start_date' => $date->startOfMonth()->format('Y-m-d'),
                'end_date' => $date->endOfMonth()->format('Y-m-d'),
                'is_unlocked' => $month <= $currentMonth,
                'is_processed' => $period ? $period->status === 'completed' : false,
                'status' => $period ? $period->status : 'pending'
            ];
        })->values();
    }

    private function getCurrentPayrollPeriod()
    {
        $period = PayrollPeriod::where('status', '!=', 'completed')
            ->latest()
            ->first();

        if (!$period) {
            // Create a new monthly period if none exists
            $period = PayrollPeriod::create([
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->endOfMonth(),
                'frequency' => 'monthly',
                'status' => 'pending'
            ]);
        }

        return [
            'id' => $period->id,
            'name' => Carbon::parse($period->start_date)->format('F Y'),
            'start_date' => $period->start_date,
            'end_date' => $period->end_date,
            'frequency' => $period->frequency,
            'is_automated' => $period->is_automated ?? false,
            'status' => $period->status
        ];
    }

    public function index()
    {
        $currentPeriod = $this->getCurrentPayrollPeriod();
        
        $employees = Employee::with(['salary'])->get()->map(function ($employee) {
            $salary = $employee->salary;
            $basic = $salary ? (float)$salary->basic_salary : 0;
            
            // Calculate mandatory deductions
            $sss = (float)min($basic * 0.045, 900); // 4.5% capped at 900
            $philhealth = (float)min(max($basic, 10000), 100000) * 0.025; // 2.5%
            $pagibig = (float)min($basic * 0.02, 100); // 2%
            $mandatoryDeductions = $sss + $philhealth + $pagibig;
            
            // Get allowances from salary record with proper type casting
            $monthlyAllowances = [
                'transportation' => (float)($salary->transportation_allowance ?? 0),
                'meal' => (float)($salary->meal_allowance ?? 0),
                'cola' => (float)($salary->cola ?? 0),
            ];
            
            $totalAllowances = array_sum($monthlyAllowances);
            $grossPay = $basic + $totalAllowances;
            
            $taxableIncome = $basic - $mandatoryDeductions;
            $tax = $this->calculateTax($taxableIncome);
            $totalDeductions = $mandatoryDeductions + $tax;
            
            return [
                'id' => $employee->id,
                'name' => $employee->full_name,
                'position' => $employee->position,
                'department' => $employee->department,
                'employeeId' => $employee->employee_id,
                'salary' => [
                    'basic' => round($basic, 2),
                    'allowances' => array_map(function($amount) {
                        return round($amount, 2);
                    }, $monthlyAllowances),
                    'deductions' => [
                        'sss' => round($sss, 2),
                        'philhealth' => round($philhealth, 2),
                        'pagibig' => round($pagibig, 2),
                        'tax' => round($tax, 2)
                    ],
                    'monthly' => [
                        'gross' => round($grossPay, 2),
                        'deductions' => round($totalDeductions, 2),
                        'net' => round($grossPay - $totalDeductions, 2)
                    ]
                ]
            ];
        });

        return Inertia::render('Payroll/Index', [
            'employees' => $employees,
            'currentPeriod' => $currentPeriod,
            'availablePeriods' => $this->getPayrollPeriods(),
            'frequencies' => [
                'monthly' => 'Monthly (Last day of month)',
                'semi-monthly' => 'Semi-monthly (15th and last day)',
                'bi-weekly' => 'Bi-weekly (Every 14 days)'
            ]
        ]);
    }

    public function processPayroll(Request $request)
    {
        $period = PayrollPeriod::findOrFail($request->period_id);
        
        if ($period->status === 'completed') {
            return back()->with('error', 'This payroll period has already been processed.');
        }

        try {
            \DB::beginTransaction();

            $period->status = 'processing';
            $period->save();

            $employees = Employee::with('salary')->get();

            foreach ($employees as $employee) {
                $salary = $employee->salary;
                $basic = $salary ? (float)$salary->basic_salary : 0;
                
                // Prorate salary if needed based on frequency
                if ($period->frequency !== 'monthly') {
                    $daysInPeriod = Carbon::parse($period->end_date)->diffInDays(Carbon::parse($period->start_date)) + 1;
                    $basic = ($basic / 30) * $daysInPeriod;
                }

                // Calculate deductions
                $sss = (float)min($basic, 30000) * 0.045;
                $philhealth = (float)min(max($basic, 10000), 100000) * 0.025;
                $pagibig = (float)min($basic * 0.02, 100);
                $mandatoryDeductions = $sss + $philhealth + $pagibig;
                
                $taxableIncome = $basic - $mandatoryDeductions;
                $tax = $this->calculateTax($taxableIncome);

                // Create payroll record
                PayrollRecord::create([
                    'employee_id' => $employee->id,
                    'payroll_period_id' => $period->id,
                    'basic_pay' => $basic,
                    'transportation_allowance' => $salary->transportation_allowance ?? 0,
                    'meal_allowance' => $salary->meal_allowance ?? 0,
                    'cola' => $salary->cola ?? 0,
                    'sss_contribution' => $sss,
                    'philhealth_contribution' => $philhealth,
                    'pagibig_contribution' => $pagibig,
                    'tax_withheld' => $tax,
                    'gross_pay' => $basic + ($salary->transportation_allowance ?? 0) + 
                                  ($salary->meal_allowance ?? 0) + ($salary->cola ?? 0),
                    'total_deductions' => $mandatoryDeductions + $tax,
                    'net_pay' => ($basic + ($salary->transportation_allowance ?? 0) + 
                                ($salary->meal_allowance ?? 0) + ($salary->cola ?? 0)) - 
                                ($mandatoryDeductions + $tax)
                ]);
            }

            $period->markAsCompleted();
            \DB::commit();

            return back()->with('success', 'Payroll has been processed successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Failed to process payroll: ' . $e->getMessage());
        }
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'frequency' => 'required|in:monthly,semi-monthly,bi-weekly',
            'is_automated' => 'required|boolean'
        ]);

        $currentPeriod = PayrollPeriod::where('status', '!=', 'completed')
            ->latest()
            ->first();

        if ($currentPeriod) {
            $currentPeriod->update($validated);
            return back()->with('success', 'Payroll settings updated successfully.');
        }

        // If no current period, create new one with settings
        PayrollPeriod::generateInitialPeriod($validated['frequency']);
        return back()->with('success', 'New payroll period created with updated settings.');
    }

    private function calculateTax($taxableIncome)
    {
        // Monthly income tax calculation based on PH tax table
        $annual = $taxableIncome * 12;
        
        if ($annual <= 250000) {
            return 0;
        } elseif ($annual <= 400000) {
            return (($annual - 250000) * 0.20) / 12;
        } elseif ($annual <= 800000) {
            return (30000 + ($annual - 400000) * 0.25) / 12;
        } elseif ($annual <= 2000000) {
            return (130000 + ($annual - 800000) * 0.30) / 12;
        } elseif ($annual <= 8000000) {
            return (490000 + ($annual - 2000000) * 0.32) / 12;
        } else {
            return (2410000 + ($annual - 8000000) * 0.35) / 12;
        }
    }

    private function getTaxBracket($monthlyTaxableIncome)
    {
        $annual = $monthlyTaxableIncome * 12;
        
        if ($annual <= 250000) {
            return 'Tax Exempt - Annual income up to ₱250,000 is exempt from withholding tax';
        } elseif ($annual <= 400000) {
            $tax = ($annual - 250000) * 0.20;
            return sprintf('20%% Tax Bracket - ₱%s annual tax (20%% of excess over ₱250,000)', number_format($tax, 2));
        } elseif ($annual <= 800000) {
            $tax = 30000 + ($annual - 400000) * 0.25;
            return sprintf('25%% Tax Bracket - ₱%s annual tax (₱30,000 + 25%% of excess over ₱400,000)', number_format($tax, 2));
        } elseif ($annual <= 2000000) {
            $tax = 130000 + ($annual - 800000) * 0.30;
            return sprintf('30%% Tax Bracket - ₱%s annual tax (₱130,000 + 30%% of excess over ₱800,000)', number_format($tax, 2));
        } elseif ($annual <= 8000000) {
            $tax = 490000 + ($annual - 2000000) * 0.32;
            return sprintf('32%% Tax Bracket - ₱%s annual tax (₱490,000 + 32%% of excess over ₱2,000,000)', number_format($tax, 2));
        } else {
            $tax = 2410000 + ($annual - 8000000) * 0.35;
            return sprintf('35%% Tax Bracket - ₱%s annual tax (₱2,410,000 + 35%% of excess over ₱8,000,000)', number_format($tax, 2));
        }
    }
}