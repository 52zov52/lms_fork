<?php

use App\Models\StudyPlan;
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
        Schema::create('study_plan_semesters', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(StudyPlan::class)
                ->cascadeOnDelete();

            $table->integer('semester_number');
            $table->integer('weeks_count')->default(0);

            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.Project_GStudyPlanSemesters
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_plan_semesters');
    }
};
