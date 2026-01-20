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
        Schema::create('admits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('dr_refer_id')->nullable();
            $table->unsignedBigInteger('refer_id')->nullable();
            $table->string('admit_at')->nullable();
            $table->string('release_at')->nullable();
            $table->string('nid')->nullable();
            $table->text('note')->nullable();

            $table->string('bed_or_cabin')->nullable();
            $table->string('father_or_spouse')->nullable();
            $table->string('received_by')->nullable();
                $table->string('clinical_diagnosis')->nullable();
            // $table->unsignedBigInteger('refer_id')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admits');
    }
};
