<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_document_order_requests', function (Blueprint $table) {
            $table->text('reject_reason')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('student_document_order_requests', function (Blueprint $table) {
            $table->dropColumn(['reject_reason', 'rejected_at', 'rejected_by']);
        });
    }
};
