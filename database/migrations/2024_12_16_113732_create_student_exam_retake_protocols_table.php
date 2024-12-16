<?php

use App\Models\User;
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
        Schema::create('student_exam_retake_protocols', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('work_study_plan_course_control_types_in_semesters_id');
            $table->foreign('work_study_plan_course_control_types_in_semesters_id', 'st_ex_rtp_wsp_course_control_types_in_sem_id_foreign')
                ->references('id')
                ->on('work_study_plan_course_control_types_in_semesters')
                ->onDelete('cascade');

            $table->integer('grade')->nullable();
            $table->integer('type_protocol')->default(1);
            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.Project_IndividualProgress
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_exam_retake_protocols');
    }
};
