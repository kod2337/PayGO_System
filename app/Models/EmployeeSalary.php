<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    protected $casts = [
        'basic_salary' => 'float',
        'daily_rate' => 'float',
        'hourly_rate' => 'float',
        'transportation_allowance' => 'float',
        'meal_allowance' => 'float',
        'cola' => 'float',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}