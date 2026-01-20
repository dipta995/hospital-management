<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->decimal('balance', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['user_id', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_balances');
    }
};
