<?php

use App\Models\Discipline;
use App\Models\StudyPlanSection;
use App\Models\WorkStudyPlan;
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
        Schema::create('work_study_plan_courses', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(WorkStudyPlan::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignIdFor(Discipline::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unsignedBigInteger('study_plan_index_id');
            $table->foreign('study_plan_index_id')
                ->references('id')
                ->on('study_plan_indexes')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unsignedBigInteger('study_plan_under_index_id');
            $table->foreign('study_plan_under_index_id')
                ->references('id')
                ->on('study_plan_under_indexes')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->integer('number_index');

            $table->foreignIdFor(StudyPlanSection::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unsignedBigInteger('work_study_plan_course_kis_id'); //Table name in IMBL BD dbo.Project_Course

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_study_plan_courses');
    }
};
