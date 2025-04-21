<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Benefit extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'thirteenth_month',
        'fourteenth_month',
        'rice_subsidy',
        'medical_allowance'
    ];

    protected $casts = [
        'thirteenth_month' => 'decimal:2',
        'fourteenth_month' => 'decimal:2',
        'rice_subsidy' => 'decimal:2',
        'medical_allowance' => 'decimal:2'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}