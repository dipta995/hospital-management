<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_lists', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_lists', 'note')) {
                $table->text('note')->nullable()->after('document');
            }
            if (!Schema::hasColumn('invoice_lists', 'followup_date')) {
                $table->date('followup_date')->nullable()->after('note');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoice_lists', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_lists', 'followup_date')) {
                $table->dropColumn('followup_date');
            }
            if (Schema::hasColumn('invoice_lists', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
};
