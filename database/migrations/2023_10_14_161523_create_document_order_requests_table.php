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
        Schema::create('document_order_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('document_type');
            $table->string('document_number', 50)->nullable();
            $table->integer('status');
            
            $table->unsignedBigInteger('requester_id');
            $table->foreign('requester_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('issuer_id')->nullable();
            $table->foreign('issuer_id')->references('id')->on('users')->onDelete('cascade');
           
            $table->dateTime('request_date');
            $table->dateTime('confirmed_date')->nullable();
            $table->dateTime('print_date')->nullable();
            $table->dateTime('issue_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_order_requests');
    }
};
