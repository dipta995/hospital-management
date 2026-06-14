<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pharmacy_products') && !Schema::hasColumn('pharmacy_products', 'status')) {
            Schema::table('pharmacy_products', function (Blueprint $table) {
                $table->boolean('status')->default(1)->after('alert_qty');
            });
        }

        if (Schema::hasTable('pharmacy_brands') && !Schema::hasColumn('pharmacy_brands', 'status')) {
            Schema::table('pharmacy_brands', function (Blueprint $table) {
                $table->boolean('status')->default(1)->after('name');
            });
        }

        if (Schema::hasTable('pharmacy_units') && !Schema::hasColumn('pharmacy_units', 'status')) {
            Schema::table('pharmacy_units', function (Blueprint $table) {
                $table->boolean('status')->default(1)->after('name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pharmacy_products', 'status')) {
            Schema::table('pharmacy_products', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }

        if (Schema::hasColumn('pharmacy_brands', 'status')) {
            Schema::table('pharmacy_brands', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }

        if (Schema::hasColumn('pharmacy_units', 'status')) {
            Schema::table('pharmacy_units', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
