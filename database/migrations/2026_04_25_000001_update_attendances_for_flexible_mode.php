<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'mode')) {
                $table->string('mode', 20)->default('standard')->after('fingerprint_data');
            }

            if (!Schema::hasColumn('attendances', 'hour_slot')) {
                $table->unsignedTinyInteger('hour_slot')->default(0)->after('mode');
            }

            // Keep normal indexes for reporting/filtering, but no uniqueness to allow many IN/OUT pairs per day.
            if (!$this->indexExists('attendances', 'attendances_employee_date_index')) {
                $table->index(['employee_id', 'date'], 'attendances_employee_date_index');
            }
        });

        if ($this->indexExists('attendances', 'attendances_employee_id_date_unique')) {
            DB::statement('ALTER TABLE attendances DROP INDEX attendances_employee_id_date_unique');
        }

        if ($this->indexExists('attendances', 'attendances_employee_date_slot_unique')) {
            DB::statement('ALTER TABLE attendances DROP INDEX attendances_employee_date_slot_unique');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if ($this->indexExists('attendances', 'attendances_employee_date_index')) {
                $table->dropIndex('attendances_employee_date_index');
            }

            if (!$this->indexExists('attendances', 'attendances_employee_id_date_unique')) {
                $table->unique(['employee_id', 'date'], 'attendances_employee_id_date_unique');
            }

            if (Schema::hasColumn('attendances', 'hour_slot')) {
                $table->dropColumn('hour_slot');
            }

            if (Schema::hasColumn('attendances', 'mode')) {
                $table->dropColumn('mode');
            }
        });
    }

    private function indexExists(string $tableName, string $indexName): bool
    {
        $databaseName = DB::getDatabaseName();

        $index = DB::table('information_schema.statistics')
            ->where('table_schema', $databaseName)
            ->where('table_name', $tableName)
            ->where('index_name', $indexName)
            ->first();

        return $index !== null;
    }
};
