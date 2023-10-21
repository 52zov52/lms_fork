<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('group', 10);
            $table->string('course', 10);
            $table->string('lesson_number', 10);
            $table->string('day', 10);
            $table->string('week', 10);
            $table->string('date', 15);
            $table->string('time', 15);
            $table->string('lesson_place', 10);
            $table->string('discipline', 255);
            $table->string('lesson_type', 10);
            $table->string('teacher', 50);
            $table->string('chair', 255);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
