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
        Schema::create('costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('cost_category_id')->nullable();
            $table->unsignedBigInteger('refer_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();//added when work on employe delete 
            $table->string('salary_id', 19)->nullable();

            $table->string('reason')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('account_details')->nullable();
            $table->string('account_type')->nullable();
            $table->string('payment_type')->default(Invoice::$paymentArray[0]);
            $table->string('creation_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    // public function down(): void
    // {
    //     Schema::dropIfExists('costs');
    // }
    public function down(): void
            {
                Schema::dropIfExists('costs');
            }


    };
