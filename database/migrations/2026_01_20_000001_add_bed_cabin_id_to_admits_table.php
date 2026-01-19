<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('admits', function (Blueprint $table) {
            $table->unsignedBigInteger('bed_cabin_id')->nullable()->after('bed_or_cabin');
        });
    }

    public function down(): void
    {
        Schema::table('admits', function (Blueprint $table) {
            $table->dropColumn('bed_cabin_id');
        });
    }
};
