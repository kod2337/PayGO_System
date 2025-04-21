<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('daily_rate', 10, 2);
            $table->decimal('hourly_rate', 10, 2);
            
            // Allowances
            $table->decimal('transportation_allowance', 10, 2)->default(0);
            $table->decimal('meal_allowance', 10, 2)->default(0);
            $table->decimal('cola', 10, 2)->default(0); // Cost of Living Allowance
            
            // Deductions
            $table->decimal('sss_contribution', 10, 2)->default(0);
            $table->decimal('philhealth_contribution', 10, 2)->default(0);
            $table->decimal('pagibig_contribution', 10, 2)->default(0);
            $table->decimal('tax_withheld', 10, 2)->default(0);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_salaries');
    }
};