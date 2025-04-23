<?php

use App\Models\Discipline;
use App\Models\Group;
use App\Models\StudyThreadNormative;
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
        Schema::create('student_respons', function (Blueprint $table) {
            $table->id();
            $table->integer('study_year');
            $table->enum('half_year', [1, 2]);
            $table->foreignIdFor(Discipline::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(StudyThreadNormative::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(Group::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_respons');
    }
};
