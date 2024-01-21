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
        Schema::create('study_plan_course_control_types_in_semesters', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('study_plan_courses_in_semester_id');
            $table->foreign('study_plan_courses_in_semester_id', 'study_plan_courses_in_semester_id_foreign')
                ->references('id')
                ->on('study_plan_courses_in_semesters')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unsignedBigInteger('control_type_id');
            $table->foreign('control_type_id', 'control_type_id_foreign')
                ->references('id')
                ->on('study_plan_courses_in_semesters')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unsignedBigInteger('study_plan_course_control_types_in_semester_kis_id'); //Table name in IMBL BD dbo.Project_GCodeControlsInSemestr
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_plan_course_control_types_in_semesters');
    }
};
