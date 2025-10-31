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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('dr_refer_id')->nullable();
            $table->unsignedBigInteger('refer_id')->nullable();
            $table->string('patient_no')->nullable();
            $table->string('invoice_number')->nullable();
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->decimal('refer_fee_total', 15, 2)->default(0.00);
            $table->decimal('refer_fee_total_agent', 15, 2)->default(0.00);
//            $table->integer('discount_percent')->default(0);
            $table->string('delivery_at')->nullable();
            $table->string('payment_type')->default(Invoice::$paymentArray[0]);
            $table->string('patient_name')->nullable();
            $table->string('patient_age_year')->nullable();
            $table->string('patient_phone')->nullable();
            $table->string('patient_email')->nullable();
            $table->string('patient_gender')->nullable();
            $table->string('patient_blood_group')->nullable();
            $table->string('patient_address')->nullable();
            $table->string('note')->nullable();
            $table->string('status')->default(Invoice::$deliveryStatusArray[0]);
            $table->string('discount_by')->nullable();
            $table->string('creation_date')->nullable();
            $table->string('dr_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
