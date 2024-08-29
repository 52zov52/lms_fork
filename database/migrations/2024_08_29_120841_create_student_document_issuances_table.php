<?php

use App\Models\StudentDocumentRequest;
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
        Schema::create('student_document_issuances', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(StudentDocumentRequest::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->string('document_number', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('first_name', 50)->nullable();
            $table->string('middle_name', 50)->nullable();
            $table->string('last_name_dative', 50)->nullable();
            $table->string('first_name_dative', 50)->nullable();
            $table->string('middle_name_dative', 50)->nullable();
            $table->integer('sex')->nullable();
            $table->integer('course')->nullable();
            $table->string('teaching_form_name', 50)->nullable();
            $table->string('speciality_name', 100)->nullable();
            $table->string('specialty_code', 50)->nullable();
            $table->string('orders', 5000)->nullable();
            $table->string('training_period', 50)->nullable();
            $table->date('check_in_date')->nullable();
            $table->date('check_out_date')->nullable();
            $table->integer('check_in_duration')->nullable();
            $table->string('employment_place', 1000)->nullable();
            $table->boolean('is_budget')->default(false);

            $table->unsignedBigInteger('confirmiter_id')->nullable();
            $table->foreign('confirmiter_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_document_issuances');
    }
};
