<?php

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
        Schema::create('work_study_plan_semesters', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(WorkStudyPlan::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('semester_number');
            $table->integer('weeks_count')->default(0);

            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.Project_StudyPlanSemesters
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_study_plan_semesters');
    }
};
