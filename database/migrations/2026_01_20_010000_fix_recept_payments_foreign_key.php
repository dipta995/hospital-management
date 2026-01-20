<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('recept_payments', function (Blueprint $table) {
            // Drop wrong foreign key that points to invoices
            $table->dropForeign('recept_payments_recept_id_foreign');

            // Recreate FK pointing to recepts table
            $table->foreign('recept_id')
                ->references('id')
                ->on('recepts')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('recept_payments', function (Blueprint $table) {
            $table->dropForeign('recept_payments_recept_id_foreign');

            $table->foreign('recept_id')
                ->references('id')
                ->on('invoices')
                ->onDelete('cascade');
        });
    }
};
