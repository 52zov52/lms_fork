<?php

use App\Models\ControlType;
use App\Models\StudyPlanCoursesInSemester;
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

            $table->unsignedBigInteger('`study_plan_courses_in_semester_id');
            $table->foreign('`study_plan_courses_in_semester_id', 'study_plan_course_and_control_type_in_semester_id_foreign')
                ->on('study_plan_courses_in_semesters')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignIdFor(ControlType::class)
                ->constrained()
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
