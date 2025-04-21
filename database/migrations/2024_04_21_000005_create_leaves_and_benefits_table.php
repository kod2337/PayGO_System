<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->integer('vacation_leave_balance')->default(0);
            $table->integer('sick_leave_balance')->default(0);
            $table->integer('emergency_leave_balance')->default(0);
            $table->timestamps();
        });

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('leave_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('reason');
            $table->timestamps();
        });

        Schema::create('benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->decimal('thirteenth_month', 10, 2)->default(0);
            $table->decimal('fourteenth_month', 10, 2)->default(0);
            $table->decimal('rice_subsidy', 10, 2)->default(0);
            $table->decimal('medical_allowance', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('benefits');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('leave_credits');
    }
};