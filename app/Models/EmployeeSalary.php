<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'basic_salary',
        'daily_rate',
        'hourly_rate',
        'transportation_allowance',
        'meal_allowance',
        'cola',
        'sss_contribution',
        'philhealth_contribution',
        'pagibig_contribution',
        'tax_withheld'
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'transportation_allowance' => 'decimal:2',
        'meal_allowance' => 'decimal:2',
        'cola' => 'decimal:2',
        'sss_contribution' => 'decimal:2',
        'philhealth_contribution' => 'decimal:2',
        'pagibig_contribution' => 'decimal:2',
        'tax_withheld' => 'decimal:2'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}