<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_document_order_issuances', function (Blueprint $table) {
            // true по умолчанию: у ранее созданных справок поведение не меняется
            $table->boolean('show_leave_orders')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('student_document_order_issuances', function (Blueprint $table) {
            $table->dropColumn('show_leave_orders');
        });
    }
};
