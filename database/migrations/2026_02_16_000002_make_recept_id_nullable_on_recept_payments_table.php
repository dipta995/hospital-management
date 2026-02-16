<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('recept_payments', function (Blueprint $table) {
            $table->dropForeign(['recept_id']);
        });

        DB::statement('ALTER TABLE recept_payments MODIFY recept_id BIGINT UNSIGNED NULL');

        Schema::table('recept_payments', function (Blueprint $table) {
            $table->foreign('recept_id')->references('id')->on('recepts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recept_payments', function (Blueprint $table) {
            $table->dropForeign(['recept_id']);
        });

        DB::statement('ALTER TABLE recept_payments MODIFY recept_id BIGINT UNSIGNED NOT NULL');

        Schema::table('recept_payments', function (Blueprint $table) {
            $table->foreign('recept_id')->references('id')->on('recepts');
        });
    }
};
