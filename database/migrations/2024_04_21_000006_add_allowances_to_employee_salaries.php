<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employee_salaries', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_salaries', 'transportation_allowance')) {
                $table->decimal('transportation_allowance', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('employee_salaries', 'meal_allowance')) {
                $table->decimal('meal_allowance', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('employee_salaries', 'cola')) {
                $table->decimal('cola', 10, 2)->default(0);
            }
        });
    }

    public function down()
    {
        Schema::table('employee_salaries', function (Blueprint $table) {
            $table->dropColumn(['transportation_allowance', 'meal_allowance', 'cola']);
        });
    }
};