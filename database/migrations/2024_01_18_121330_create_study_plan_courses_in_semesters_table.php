<?php

use App\Models\StudyPlanCourse;
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
        Schema::create('study_plan_courses_in_semesters', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(StudyPlanCourse::class)
                ->cascadeOnDelete();
            
            $table->integer('semester_number');
            $table->integer('lecture_hours');
            $table->integer('practical_hours');
            $table->integer('laboratory_hours');
            $table->integer('self_study_hours');
            $table->integer('attestation_work_count')->nullable();
            $table->integer('credits')->nullable();
            $table->integer('weeks')->nullable();
            $table->integer('hours_max_load')->default(0);
            $table->integer('course_work_hours')->nullable();
            $table->integer('test')->nullable();
            $table->integer('week_hours')->nullable();
            $table->float('work_content_first')->nullable();
            $table->float('work_content_second')->nullable();
            $table->integer('individual_consultation_hours')->nullable();
            $table->integer('group_consultation_hours')->nullable();
            $table->integer('control_hours')->nullable();
            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.Project_GCodeCourseInSemestr
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_plan_courses_in_semesters');
    }
};
