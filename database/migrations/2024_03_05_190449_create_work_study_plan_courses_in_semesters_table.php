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
        Schema::create('work_study_plan_courses_in_semesters', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('work_study_plan_course_id');
            $table->foreign('work_study_plan_course_id', 'work_study_plan_course_id_foreign')
                ->references('id')
                ->on('work_study_plan_courses')
                ->onDelete('cascade');

            $table->integer('semester_number')->default(0);
            $table->integer('lecture_hours')->default(0);
            $table->integer('practical_hours')->default(0);
            $table->integer('laboratory_hours')->default(0);
            $table->integer('self_study_hours')->default(0);
            $table->integer('attestation_work_count')->default(0);
            $table->integer('credits')->default(0);
            $table->integer('weeks')->default(0);
            $table->integer('hours_max_load')->default(0);
            $table->integer('course_work_hours')->default(0);
            $table->integer('test')->default(0);
            $table->integer('week_hours')->default(0);
            $table->float('work_content_first')->default(0);
            $table->float('work_content_second')->default(0);
            $table->integer('individual_consultation_hours')->default(0);
            $table->integer('group_consultation_hours')->default(0);
            $table->integer('control_hours')->default(0);
            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.Project_CodeCourseInSemestr
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_study_plan_courses_in_semesters');
    }
};
