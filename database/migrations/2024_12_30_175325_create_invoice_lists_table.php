<?php

use App\Models\Invoice;
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
        Schema::create('invoice_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->unsignedBigInteger('product_id');
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('discount_price', 15, 2)->nullable();
            $table->decimal('refer_fee', 15, 2)->nullable();
            $table->longText('test_report')->nullable();
            $table->string('document')->nullable();
            $table->string('status')->default(\App\Models\InvoiceList::$statusArray[0]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_lists');
    }
};
