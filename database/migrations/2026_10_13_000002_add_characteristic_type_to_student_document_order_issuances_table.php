<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_document_order_issuances', function (Blueprint $table) {
            $table->string('characteristic_type', 20)->default('positive');
        });
    }

    public function down(): void
    {
        Schema::table('student_document_order_issuances', function (Blueprint $table) {
            $table->dropColumn('characteristic_type');
        });
    }
};
