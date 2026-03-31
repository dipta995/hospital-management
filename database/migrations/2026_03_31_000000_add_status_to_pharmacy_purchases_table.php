<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pharmacy_purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('pharmacy_purchases', 'status')) {
                $table->string('status', 50)->default('Pending')->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pharmacy_purchases', function (Blueprint $table) {
            if (Schema::hasColumn('pharmacy_purchases', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
