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
        Schema::create('work_study_plan_course_control_types_in_semesters', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('work_study_plan_courses_in_semester_id');
            $table->foreign('work_study_plan_courses_in_semester_id', 'work_study_plan_courses_in_semester_id_foreign')
                ->references('id')
                ->on('work_study_plan_courses_in_semesters')
                ->onDelete('cascade');

            $table->unsignedBigInteger('control_type_id');
            $table->foreign('control_type_id', 'work_study_plan_control_type_id_foreign')
                ->references('id')
                ->on('control_types')
                ->onDelete('cascade');

            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.Project_CodeControlsInSemestr
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_study_plan_course_control_types_in_semesters');
    }
};
