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
        Schema::table('recept_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('admit_id')->nullable()->after('recept_id');
            $table->foreign('admit_id')->references('id')->on('admits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recept_payments', function (Blueprint $table) {
            $table->dropForeign(['admit_id']);
            $table->dropColumn('admit_id');
        });
    }
};
