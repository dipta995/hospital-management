<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pharmacy_sales', function (Blueprint $table) {
            $table->unsignedBigInteger('dr_refer_id')->nullable()->after('customer_id');
        });
    }

    public function down(): void
    {
        Schema::table('pharmacy_sales', function (Blueprint $table) {
            $table->dropColumn('dr_refer_id');
        });
    }
};
