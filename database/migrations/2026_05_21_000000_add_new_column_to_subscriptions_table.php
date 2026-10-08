<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('subscriptions') || Schema::hasColumn('subscriptions', 'payment_amount')) {
            return;
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->decimal('payment_amount', 15, 2)->nullable();
        });
    }

    public function down()
    {
        if (!Schema::hasTable('subscriptions') || !Schema::hasColumn('subscriptions', 'payment_amount')) {
            return;
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('payment_amount');
        });
    }
};
