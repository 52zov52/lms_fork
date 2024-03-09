<?php

use App\Models\LevelEducation;
use App\Models\Qualification;
use App\Models\Speciality;
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
        Schema::create('study_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_name', 100);

            $table->foreignIdFor(Speciality::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(TeachingForm::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(LevelEducation::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(Qualification::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('number_semesters');
            $table->integer('number_years');
            $table->integer('number_months');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->dateTime('creation_date');
            $table->dateTime('approval_date');
            $table->unsignedBigInteger('external_id')->nullable(); //Table name in IMBL BD dbo.Project_GStudyPlan
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_plans');
    }
};
