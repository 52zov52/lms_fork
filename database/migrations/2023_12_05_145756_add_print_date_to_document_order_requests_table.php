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
        Schema::table('document_order_requests', function (Blueprint $table) {
            $table->dateTime('print_date')->nullable()->after('confirmed_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_order_requests', function (Blueprint $table) {
            $table->dropColumn('print_date');
        });
    }
};
