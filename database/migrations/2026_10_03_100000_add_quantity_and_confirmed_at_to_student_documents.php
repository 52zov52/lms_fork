<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_document_order_requests', function (Blueprint $table) {
            $table->unsignedSmallInteger('quantity')->default(1)->after('half_year');
        });
 
        Schema::table('student_document_order_issuances', function (Blueprint $table) {
            $table->timestamp('confirmed_at')->nullable()->after('confirmiter_id');
        });
 
        // Для уже подтверждённых справок дата подтверждения = последнее обновление записи
        DB::table('student_document_order_issuances')
            ->whereNotNull('confirmiter_id')
            ->update(['confirmed_at' => DB::raw('updated_at')]);
    }
 
    public function down(): void
    {
        Schema::table('student_document_order_requests', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
 
        Schema::table('student_document_order_issuances', function (Blueprint $table) {
            $table->dropColumn('confirmed_at');
        });
    }
};