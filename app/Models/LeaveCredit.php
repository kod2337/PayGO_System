<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveCredit extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'vacation_leave_balance',
        'sick_leave_balance',
        'emergency_leave_balance'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}