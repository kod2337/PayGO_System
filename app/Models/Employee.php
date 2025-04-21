<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'birth_date',
        'gender',
        'civil_status',
        'nationality',
        'address',
        'contact_number',
        'emergency_contact_name',
        'emergency_contact_number',
        'tin_number',
        'sss_number',
        'philhealth_number',
        'pagibig_number',
        'date_hired',
        'employment_status',
        'department',
        'position',
        'is_active'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'date_hired' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function salary()
    {
        return $this->hasOne(EmployeeSalary::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function payrollRecords()
    {
        return $this->hasMany(PayrollRecord::class);
    }

    public function leaveCredits()
    {
        return $this->hasOne(LeaveCredit::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function benefits()
    {
        return $this->hasOne(Benefit::class);
    }

    // Full name accessor
    public function getFullNameAttribute()
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
            $this->suffix
        ])));
    }
}