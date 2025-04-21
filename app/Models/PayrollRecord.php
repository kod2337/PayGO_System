<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'payroll_period_id',
        'basic_pay',
        'transportation_allowance',
        'meal_allowance',
        'cola',
        'overtime_pay',
        'night_differential_pay',
        'holiday_pay',
        'sss_contribution',
        'philhealth_contribution',
        'pagibig_contribution',
        'tax_withheld',
        'loans',
        'other_deductions',
        'gross_pay',
        'total_deductions',
        'net_pay'
    ];

    protected $casts = [
        'basic_pay' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'net_pay' => 'decimal:2'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class);
    }
}