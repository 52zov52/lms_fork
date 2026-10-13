<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_document_order_issuances', function (Blueprint $table) {
            // Что выбрал студент при заказе; если сотрудник переопределит — останется здесь
            $table->string('original_characteristic_type', 20)->nullable()->after('characteristic_type');
        });
    }

    public function down(): void
    {
        Schema::table('student_document_order_issuances', function (Blueprint $table) {
            $table->dropColumn('original_characteristic_type');
        });
    }
};