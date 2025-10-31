<?php

use App\Models\DoctorSerial;
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
        Schema::create('doctor_serials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('reefer_id');
            $table->string('patient_name')->nullable();
            $table->string('patient_age_year')->nullable();
            $table->string('patient_phone')->nullable();
            $table->string('patient_email')->nullable();
            $table->string('patient_gender')->nullable();
            $table->string('patient_blood_group')->nullable();
            $table->string('patient_address')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('remarks')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('date')->default(DB::raw('CURRENT_DATE'));
            $table->string('status')->default(DoctorSerial::$statusArray[0])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_serials');
    }
};
