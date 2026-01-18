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
        Schema::table('admits', function (Blueprint $table) {
            if (!Schema::hasColumn('admits', 'refer_id')) {
                $table->unsignedBigInteger('refer_id')->nullable()->after('branch_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admits', function (Blueprint $table) {
            if (Schema::hasColumn('admits', 'refer_id')) {
                $table->dropColumn('refer_id');
            }
        });
    }
};
