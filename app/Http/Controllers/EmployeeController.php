<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\Benefit;
use App\Models\LeaveCredit;
use App\Models\PayrollRecord;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeController extends Controller
{

    public function index()
    {
        $employees = Employee::with(['salary', 'benefits', 'payrollRecords' => function($query) {
            $query->latest()->take(1);
        }])->get()
            ->map(function ($employee) {
                $salary = $employee->salary;
                $basic = $salary ? (float)$salary->basic_salary : 0;
                
                // Calculate monthly deductions
                $sss = $basic * 0.045; // 4.5%
                $philhealth = min(max($basic, 10000), 100000) * 0.025; // 2.5%
                $pagibig = min($basic * 0.02, 100); // 2%
                $totalDeductions = $sss + $philhealth + $pagibig;

                // Calculate monthly allowances
                $monthlyAllowances = [
                    'transportation' => 1000,
                    'meal' => 1500,
                    'cola' => 1000,
                ];
                $totalAllowances = array_sum($monthlyAllowances);

                // Calculate 13th month pay (but don't include in monthly net)
                $thirteenthMonth = $basic / 12;

                return [
                    'id' => $employee->id,
                    'name' => $employee->full_name,
                    'position' => $employee->position,
                    'department' => $employee->department,
                    'employeeId' => $employee->employee_id,
                    'status' => $employee->employment_status,
                    'salary' => [
                        'basic' => $basic,
                        'allowances' => $monthlyAllowances,
                        'deductions' => [
                            'sss' => $sss,
                            'philhealth' => $philhealth,
                            'pagibig' => $pagibig,
                            'withholding' => 0, // TODO: Implement tax calculation
                        ],
                        'benefits' => [
                            'thirteenthMonth' => $thirteenthMonth, // Displayed but not included in monthly net
                            'riceBenefit' => 1500,
                        ],
                        'monthly' => [
                            'gross' => $basic + $totalAllowances,
                            'totalDeductions' => $totalDeductions,
                            'net' => ($basic + $totalAllowances) - $totalDeductions
                        ],
                        'annual' => [
                            'thirteenthMonth' => $thirteenthMonth * 12 // Full 13th month pay
                        ]
                    ]
                ];
            });

        return Inertia::render('Employees/Index', [
            'employees' => $employees
        ]);
    }

    public function create()
    {
        return Inertia::render('Employees/Create', [
            'departments' => [
                'IT Department',
                'HR Department',
                'Finance Department',
                'Operations Department',
                'Marketing Department'
            ],
            'employmentStatuses' => [
                'Regular',
                'Probationary',
                'Contract'
            ]
        ]);
    }

    public function store(Request $request)
    {
        \Log::info('Creating new employee:', $request->all());

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:10',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female',
            'civil_status' => 'required|string',
            'nationality' => 'required|string',
            'address' => 'required|string',
            'contact_number' => 'required|string',
            'emergency_contact_name' => 'required|string',
            'emergency_contact_number' => 'required|string',
            'tin_number' => 'nullable|string',
            'sss_number' => 'nullable|string',
            'philhealth_number' => 'nullable|string',
            'pagibig_number' => 'nullable|string',
            'employment_status' => 'required|in:Regular,Probationary,Contract',
            'department' => 'required|string',
            'position' => 'required|string',
            'date_hired' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
        ]);

        try {
            \DB::beginTransaction();

            // Generate employee ID
            $latestEmployee = Employee::latest()->first();
            $nextId = $latestEmployee ? intval(substr($latestEmployee->employee_id, 8)) + 1 : 1;
            $employeeId = 'EMP-' . date('Y') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

            // Create employee
            $employee = Employee::create(array_merge(
                $request->except('basic_salary'),
                ['employee_id' => $employeeId]
            ));

            \Log::info('Employee created:', ['id' => $employee->id]);

            // Create salary record
            $salary = EmployeeSalary::create([
                'employee_id' => $employee->id,
                'basic_salary' => $request->basic_salary,
                'daily_rate' => $request->basic_salary / 22,
                'hourly_rate' => ($request->basic_salary / 22) / 8,
            ]);

            \Log::info('Salary record created:', ['id' => $salary->id]);

            // Calculate mandatory deductions
            $sss = $request->basic_salary * 0.045; // 4.5%
            $philhealth = min(max($request->basic_salary, 10000), 100000) * 0.025; // 2.5%
            $pagibig = min($request->basic_salary * 0.02, 100); // 2%
            $totalDeductions = $sss + $philhealth + $pagibig;

            // Create initial payroll record
            PayrollRecord::create([
                'employee_id' => $employee->id,
                'basic_pay' => $request->basic_salary,
                'sss_contribution' => $sss,
                'philhealth_contribution' => $philhealth,
                'pagibig_contribution' => $pagibig,
                'gross_pay' => $request->basic_salary,
                'total_deductions' => $totalDeductions,
                'net_pay' => $request->basic_salary - $totalDeductions
            ]);

            \Log::info('Payroll record created:', ['employee_id' => $employee->id]);

            // Create benefits record
            $benefits = Benefit::create([
                'employee_id' => $employee->id,
                'thirteenth_month' => 0,
                'rice_subsidy' => 1500,
                'medical_allowance' => 1000,
            ]);

            \Log::info('Benefits record created:', ['id' => $benefits->id]);

            \DB::commit();

            return redirect()->route('employees.index')
                ->with('message', 'Employee added successfully');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error creating employee:', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}