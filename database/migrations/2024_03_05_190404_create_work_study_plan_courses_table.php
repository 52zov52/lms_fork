<?php

use App\Models\Discipline;
use App\Models\Section;
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
                ->cascadeOnDelete();

            $table->foreignIdFor(Discipline::class)
                ->cascadeOnDelete();

            $table->unsignedBigInteger('index_id');
            $table->foreign('index_id')->references('id')->on('indexes')->onDelete('cascade');

            $table->unsignedBigInteger('under_index_id');
            $table->foreign('under_index_id')->references('id')->on('under_indexes')->onDelete('cascade');

            $table->integer('number_index');

            $table->foreignIdFor(Section::class)
                ->cascadeOnDelete();

            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.Project_Course

            $table->timestamps();
            $table->softDeletes();
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
