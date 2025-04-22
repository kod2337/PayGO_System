<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PayrollPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_date',
        'end_date',
        'frequency',
        'is_automated',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_automated' => 'boolean',
    ];

    public static function generateInitialPeriod($frequency)
    {
        $now = now();
        $startDate = $now->copy()->startOfMonth();
        $endDate = $now->copy()->endOfMonth();

        if ($frequency === 'semi-monthly') {
            if ($now->day <= 15) {
                $endDate = $now->copy()->setDay(15);
            } else {
                $startDate = $now->copy()->setDay(16);
            }
        } elseif ($frequency === 'bi-weekly') {
            $startDate = $now->copy()->startOfWeek();
            $endDate = $startDate->copy()->addDays(13);
        }

        return self::create([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'frequency' => $frequency,
            'status' => 'pending',
            'is_automated' => false
        ]);
    }

    public function payrollRecords()
    {
        return $this->hasMany(PayrollRecord::class);
    }

    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->save();

        // If automated, create next period
        if ($this->is_automated) {
            $this->createNextPeriod();
        }
    }

    public function createNextPeriod()
    {
        $nextStart = $this->end_date->addDay();
        $nextEnd = null;

        switch ($this->frequency) {
            case 'monthly':
                $nextEnd = $nextStart->copy()->endOfMonth();
                break;
            case 'semi-monthly':
                if ($nextStart->day === 1) {
                    $nextEnd = $nextStart->copy()->setDay(15);
                } else {
                    $nextEnd = $nextStart->copy()->endOfMonth();
                }
                break;
            case 'bi-weekly':
                $nextEnd = $nextStart->copy()->addDays(13);
                break;
        }

        return self::create([
            'start_date' => $nextStart,
            'end_date' => $nextEnd,
            'frequency' => $this->frequency,
            'is_automated' => $this->is_automated,
            'status' => 'pending'
        ]);
    }

    public function getDaysInPeriod()
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getWorkdaysInPeriod()
    {
        $workdays = 0;
        $current = $this->start_date->copy();
        
        while ($current->lte($this->end_date)) {
            if ($current->isWeekday()) {
                $workdays++;
            }
            $current->addDay();
        }
        
        return $workdays;
    }
}
