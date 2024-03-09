<?php

use App\Models\Branch;
use App\Models\LevelEducation;
use App\Models\Speciality;
use App\Models\StudyPlan;
use App\Models\TeachingForm;
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
        Schema::create('work_study_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_name', 100);

            $table->foreignIdFor(Speciality::class)
                ->cascadeOnDelete();

            $table->foreignIdFor(TeachingForm::class)
                ->cascadeOnDelete();

            $table->foreignIdFor(LevelEducation::class)
                ->cascadeOnDelete();

            $table->foreignIdFor(Branch::class)
                ->cascadeOnDelete();

            $table->dateTime('creation_date');
            $table->dateTime('approval_date');
            
            $table->foreignIdFor(StudyPlan::class)
                ->cascadeOnDelete();
            
            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.Project_StudyPlan
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_study_plans');
    }
};
