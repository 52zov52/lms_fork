<?php

use App\Models\Group;
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
        Schema::create('group_study_thread_subgroup', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Group::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('student_count');

            $table->unsignedBigInteger('work_study_plan_courses_in_semester_id');
            $table->foreign('work_study_plan_courses_in_semester_id', 'study_threads_wsp_courses_in_semester_id_foreign')
                ->references('id')
                ->on('work_study_plan_courses_in_semesters')
                ->onDelete('cascade');

            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.StudyThread_Trainer
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_study_thread_subgroup');
    }
};
