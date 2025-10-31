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
        Schema::create('reefers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('name')->nullable();
            $table->string('designation')->nullable();
            $table->string('phone')->nullable();
            $table->integer('percent')->default(0);
            $table->string('type');
            $table->string('email')->nullable();
            $table->string('username')->nullable();
            $table->string('office_time')->nullable();
            $table->string('password')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reefers');
    }
};
