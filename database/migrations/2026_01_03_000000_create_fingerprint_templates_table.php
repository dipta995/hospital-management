<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('fingerprint_templates', function (Blueprint $table) {
            $table->id();
            $table->integer('finger_id')->unique();
            $table->longText('template');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fingerprint_templates');
    }
};
