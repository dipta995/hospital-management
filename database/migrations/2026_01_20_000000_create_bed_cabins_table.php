<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bed_cabins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('name'); // Bed/Cabin number or name
            $table->enum('type', ['bed', 'cabin'])->default('bed');
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->decimal('price', 10, 2)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bed_cabins');
    }
};
