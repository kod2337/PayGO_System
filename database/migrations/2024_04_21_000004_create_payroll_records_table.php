<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('pending'); // pending, processing, completed
            $table->string('frequency')->default('monthly'); // monthly, semi-monthly, bi-weekly
            $table->boolean('is_automated')->default(false);
            $table->date('next_schedule')->nullable();
            $table->timestamps();
        });

        Schema::create('payroll_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('payroll_period_id')->constrained('payroll_periods')->onDelete('cascade');
            
            // Basic Pay
            $table->decimal('basic_pay', 10, 2);
            
            // Allowances
            $table->decimal('transportation_allowance', 10, 2)->default(0);
            $table->decimal('meal_allowance', 10, 2)->default(0);
            $table->decimal('cola', 10, 2)->default(0);
            
            // Overtime and Other Pay
            $table->decimal('overtime_pay', 10, 2)->default(0);
            $table->decimal('night_differential_pay', 10, 2)->default(0);
            $table->decimal('holiday_pay', 10, 2)->default(0);
            
            // Government Contributions
            $table->decimal('sss_contribution', 10, 2)->default(0);
            $table->decimal('philhealth_contribution', 10, 2)->default(0);
            $table->decimal('pagibig_contribution', 10, 2)->default(0);
            $table->decimal('tax_withheld', 10, 2)->default(0);
            
            // Other Deductions
            $table->decimal('loans', 10, 2)->default(0);
            $table->decimal('other_deductions', 10, 2)->default(0);
            
            // Totals
            $table->decimal('gross_pay', 10, 2);
            $table->decimal('total_deductions', 10, 2);
            $table->decimal('net_pay', 10, 2);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_records');
        Schema::dropIfExists('payroll_periods');
    }
};