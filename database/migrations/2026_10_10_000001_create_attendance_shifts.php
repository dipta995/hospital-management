<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('attendance_shifts')) {
            Schema::create('attendance_shifts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->index();
                $table->string('name', 100);
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->unsignedSmallInteger('break_minutes')->default(0);
                $table->boolean('is_flexible')->default(false);
                $table->decimal('flexible_hours', 4, 2)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('employees') && !Schema::hasColumn('employees', 'shift_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->unsignedBigInteger('shift_id')->nullable()->index();
            });
        }

        if (Schema::hasTable('attendances')) {
            Schema::table('attendances', function (Blueprint $table) {
                if (!Schema::hasColumn('attendances', 'shift_id')) {
                    $table->unsignedBigInteger('shift_id')->nullable();
                }
                if (!Schema::hasColumn('attendances', 'source')) {
                    $table->string('source', 20)->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('attendances', 'source') || Schema::hasColumn('attendances', 'shift_id')) {
            Schema::table('attendances', function (Blueprint $table) {
                foreach (['source', 'shift_id'] as $column) {
                    if (Schema::hasColumn('attendances', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasColumn('employees', 'shift_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('shift_id');
            });
        }

        Schema::dropIfExists('attendance_shifts');
    }
};
