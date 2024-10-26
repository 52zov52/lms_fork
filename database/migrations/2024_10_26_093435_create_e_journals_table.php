<?php

use App\Models\Group;
use App\Models\WorkStudyPlanCoursesInSemester;
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
        Schema::create('e_journals', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('teacher_id');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');

            $table->foreignIdFor(WorkStudyPlanCoursesInSemester::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(Group::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');

            $table->integer('status')->default(2);
            $table->date('lesson_date');
            $table->string('lesson_time');
            $table->integer('lesson_type');
            $table->string('lesson_theme', 1500);

            $table->unsignedBigInteger('who_checked')->nullable();
            $table->foreign('who_checked')->references('id')->on('users')->onDelete('cascade');

            $table->dateTime('checked_at')->nullable();
            $table->boolean('is_signature')->default(false);

            $table->unsignedBigInteger('who_signed')->nullable();
            $table->foreign('who_signed')->references('id')->on('users')->onDelete('cascade');

            $table->dateTime('signatured_at')->nullable();
            $table->string('academic_year');
            $table->integer('half_year');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_journals');
    }
};
