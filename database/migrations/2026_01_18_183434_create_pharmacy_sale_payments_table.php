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
        Schema::create('pharmacy_sale_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pharmacy_sale_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('admin_id');
            $table->decimal('paid_amount', 10, 2);
            $table->date('creation_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_sale_payments');
    }
};
