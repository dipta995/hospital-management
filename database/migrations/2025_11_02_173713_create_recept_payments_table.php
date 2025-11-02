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
        Schema::create('recept_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recept_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('admin_id');
            $table->foreign('recept_id')->references('id')->on('invoices');
            $table->decimal('paid_amount', 15, 2);
            $table->string('creation_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recept_payments');
    }
};
