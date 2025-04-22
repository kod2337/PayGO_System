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
        'status',
        'frequency',
        'is_automated',
        'next_schedule'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_schedule' => 'date',
        'is_automated' => 'boolean'
    ];

    public function payrollRecords()
    {
        return $this->hasMany(PayrollRecord::class);
    }

    public static function generateInitialPeriod($frequency = 'monthly')
    {
        $now = now();
        $start = $now->copy()->startOfMonth();
        $end = $now->copy()->endOfMonth();

        return static::create([
            'start_date' => $start,
            'end_date' => $end,
            'status' => 'pending',
            'frequency' => $frequency,
            'is_automated' => false
        ]);
    }

    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->save();
        
        // Create next period if automated
        if ($this->is_automated) {
            $nextPeriod = $this->calculateNextPeriod();
            static::create($nextPeriod);
        }
    }

    public function calculateNextPeriod()
    {
        $start = Carbon::parse($this->end_date)->addDay();
        
        switch ($this->frequency) {
            case 'bi-weekly':
                $end = $start->copy()->addDays(13);
                break;
            case 'semi-monthly':
                // If start is 1st, end on 15th. If start is 16th, end on last day of month
                if ($start->day === 1) {
                    $end = $start->copy()->setDay(15);
                } else {
                    $end = $start->copy()->endOfMonth();
                }
                break;
            case 'monthly':
            default:
                $end = $start->copy()->endOfMonth();
                break;
        }

        return [
            'start_date' => $start,
            'end_date' => $end,
            'frequency' => $this->frequency,
            'is_automated' => $this->is_automated,
            'status' => 'pending',
            'next_schedule' => $end->copy()->addDay()
        ];
    }
}
