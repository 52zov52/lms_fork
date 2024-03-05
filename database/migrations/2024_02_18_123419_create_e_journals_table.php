<?php

use App\Models\CourseTeacher;
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
        Schema::create('e_journals', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('status')->default(2);
            $table->date('lesson_date');
            $table->string('lesson_time');
            $table->integer('lesson_type');
            $table->string('lesson_theme', 1500);

            $table->unsignedBigInteger('course_teacher_id');
            $table->foreign('course_teacher_id')->references('id')->on('course_teacher')->onUpdate('cascade')->onDelete('cascade');

            $table->boolean('is_signature')->default(false);
            
            $table->unsignedBigInteger('who_signed');
            $table->foreign('who_signed')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->dataTime('signatured_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_journals');
    }
};
