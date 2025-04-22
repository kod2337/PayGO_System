<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payroll_periods', function (Blueprint $table) {
            $table->enum('frequency', ['monthly', 'semi-monthly', 'bi-weekly'])->default('monthly')->after('status');
            $table->boolean('is_automated')->default(false)->after('frequency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       
      
    }
};