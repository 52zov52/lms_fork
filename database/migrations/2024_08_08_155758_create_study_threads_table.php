<?php

use App\Models\ChairDiscipline;
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
        Schema::create('study_threads', function (Blueprint $table) {
            $table->id();
            $table->string('name', 1500);

            $table->unsignedBigInteger('chair_discipline_id');
            $table->foreign('chair_discipline_id')
                ->references('id')
                ->on('chair_discipline')
                ->onDelete('cascade');

            $table->foreignIdFor(StudyThreadNormative::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('year');
            $table->integer('half_year');
            $table->integer('calculation_type');
            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.StudyThread
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_threads');
    }
};
