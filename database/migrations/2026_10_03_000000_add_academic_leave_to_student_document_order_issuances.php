<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_document_order_issuances', function (Blueprint $table) {
            $table->boolean('had_academic_leave')->default(false)->after('is_budget');
            $table->text('leave_orders')->nullable()->after('had_academic_leave');
        });
    }
 
    public function down(): void
    {
        Schema::table('student_document_order_issuances', function (Blueprint $table) {
            $table->dropColumn(['had_academic_leave', 'leave_orders']);
        });
    }
};