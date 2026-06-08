<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'weekly_off_days')) {
                $table->json('weekly_off_days')->nullable()->after('status');
            }
            if (!Schema::hasColumn('employees', 'working_hours_per_day')) {
                $table->decimal('working_hours_per_day', 4, 2)->default(8)->after('weekly_off_days');
            }
            if (!Schema::hasColumn('employees', 'annual_leave_quota')) {
                $table->unsignedSmallInteger('annual_leave_quota')->default(12)->after('working_hours_per_day');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $columns = ['weekly_off_days', 'working_hours_per_day', 'annual_leave_quota'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
