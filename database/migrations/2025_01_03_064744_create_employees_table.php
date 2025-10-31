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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('phone')->nullable();
            $table->decimal('salary', 15, 2)->nullable();
            $table->string('status')->default('Active');
            $table->string('rfid')->nullable();
            $table->timestamps();
           

            // $table->decimal('total_costs', 15, 2)->default(0)->after('salary');// new add 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
        //  Schema::table('employees', function (Blueprint $table) {
        //     $table->dropColumn('total_costs');
        // });
    }
};
