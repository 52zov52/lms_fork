<?php

use App\Models\Group;
use App\Models\TypeBulletin;
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
        Schema::create('bulletins', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Group::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('work_study_plan_course_control_types_in_semesters_id');
            $table->foreign('work_study_plan_course_control_types_in_semesters_id', 'bulletins_wsp_courses_in_semester_id_foreign')
                ->references('id')
                ->on('work_study_plan_course_control_types_in_semesters')
                ->onDelete('cascade');

            $table->foreignIdFor(TypeBulletin::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('bulletin_number');

            $table->datetime('opening_date');
            $table->datetime('closing_date');
            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.Project_Bulletin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulletins');
    }
};
