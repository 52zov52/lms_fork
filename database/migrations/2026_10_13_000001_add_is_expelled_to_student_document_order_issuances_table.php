<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_document_order_issuances', function (Blueprint $table) {
            $table->boolean('is_expelled')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('student_document_order_issuances', function (Blueprint $table) {
            $table->dropColumn('is_expelled');
        });
    }
};
