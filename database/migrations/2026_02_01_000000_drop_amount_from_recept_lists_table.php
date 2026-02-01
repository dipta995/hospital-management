<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recept_lists', function (Blueprint $table) {
            if (Schema::hasColumn('recept_lists', 'amount')) {
                $table->dropColumn('amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('recept_lists', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->nullable();
        });
    }
};
