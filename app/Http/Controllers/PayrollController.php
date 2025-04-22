<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PayrollRecord;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PayrollController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['salary', 'payrollRecords' => function($query) {
            $query->latest()->take(1);
        }])->get()
        ->map(function ($employee) {
            $salary = $employee->salary;
            $basic = $salary ? (float)$salary->basic_salary : 0;
            
            // Calculate monthly deductions
            $sss = (float)min($basic, 30000) * 0.045; // 4.5% with 30k cap
            $philhealth = (float)min(max($basic, 10000), 100000) * 0.025; // 2.5%
            $pagibig = (float)min($basic * 0.02, 100); // 2% capped at 100
            $mandatoryDeductions = $sss + $philhealth + $pagibig;

            // Calculate taxable income
            $taxableIncome = (float)($basic - $mandatoryDeductions);
            
            // Calculate withholding tax
            $tax = (float)$this->calculateTax($taxableIncome);

            // Calculate monthly allowances
            $monthlyAllowances = [
                'transportation' => (float)($salary->transportation_allowance ?? 0),
                'meal' => (float)($salary->meal_allowance ?? 0),
                'cola' => (float)($salary->cola ?? 0),
            ];
            $totalAllowances = (float)array_sum($monthlyAllowances);

            // Get latest payroll record if exists
            $latestPayroll = $employee->payrollRecords->first();

            $totalDeductions = (float)($mandatoryDeductions + $tax);
            $grossPay = (float)($basic + $totalAllowances);
            $netPay = (float)($grossPay - $totalDeductions);

            return [
                'id' => $employee->id,
                'name' => $employee->full_name,
                'position' => $employee->position,
                'department' => $employee->department,
                'employeeId' => $employee->employee_id,
                'salary' => [
                    'basic' => round($basic, 2),
                    'allowances' => array_map(function($amount) {
                        return round((float)$amount, 2);
                    }, $monthlyAllowances),
                    'deductions' => [
                        'sss' => round($sss, 2),
                        'philhealth' => round($philhealth, 2),
                        'pagibig' => round($pagibig, 2),
                        'tax' => round($tax, 2),
                        'total' => round($totalDeductions, 2)
                    ],
                    'computation' => [
                        'taxableIncome' => round($taxableIncome, 2),
                        'taxBracket' => $this->getTaxBracket($taxableIncome),
                        'mandatoryDeductions' => round($mandatoryDeductions, 2),
                    ],
                    'grossPay' => round($grossPay, 2),
                    'netPay' => round($netPay, 2),
                ],
                'latestPayroll' => $latestPayroll
            ];
        });

        $currentPeriod = PayrollPeriod::where('status', 'processing')
            ->first() ?? [
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->endOfMonth(),
                'status' => 'pending'
            ];

        return Inertia::render('Payroll/Index', [
            'employees' => $employees,
            'currentPeriod' => $currentPeriod
        ]);
    }

    private function calculateTax($monthlyTaxableIncome)
    {
        if ($monthlyTaxableIncome <= 20833) { // 250,000 / 12
            return 0;
        } elseif ($monthlyTaxableIncome <= 33333) { // 400,000 / 12
            return ($monthlyTaxableIncome - 20833) * 0.20;
        } elseif ($monthlyTaxableIncome <= 66667) { // 800,000 / 12
            return 2500 + ($monthlyTaxableIncome - 33333) * 0.25;
        } elseif ($monthlyTaxableIncome <= 166667) { // 2,000,000 / 12
            return 10833.33 + ($monthlyTaxableIncome - 66667) * 0.30;
        } elseif ($monthlyTaxableIncome <= 666667) { // 8,000,000 / 12
            return 40833.33 + ($monthlyTaxableIncome - 166667) * 0.32;
        } else {
            return 200833.33 + ($monthlyTaxableIncome - 666667) * 0.35;
        }
    }

    private function getTaxBracket($monthlyTaxableIncome)
    {
        if ($monthlyTaxableIncome <= 20833) {
            return 'Tax Exempt (0%)';
        } elseif ($monthlyTaxableIncome <= 33333) {
            return '20% of excess over ₱250,000 annually';
        } elseif ($monthlyTaxableIncome <= 66667) {
            return '₱30,000 + 25% of excess over ₱400,000 annually';
        } elseif ($monthlyTaxableIncome <= 166667) {
            return '₱130,000 + 30% of excess over ₱800,000 annually';
        } elseif ($monthlyTaxableIncome <= 666667) {
            return '₱490,000 + 32% of excess over ₱2,000,000 annually';
        } else {
            return '₱2,410,000 + 35% of excess over ₱8,000,000 annually';
        }
    }
}