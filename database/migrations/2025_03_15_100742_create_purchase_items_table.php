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
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('purchase_id');
            $table->integer('quantity');
            $table->integer('quantity_spend')->default(0);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
