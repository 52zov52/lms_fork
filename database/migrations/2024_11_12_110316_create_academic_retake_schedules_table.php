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
        Schema::create('academic_retake_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete(); //ИД преподавателя
            $table->date('retake_date'); //дата пересдачи
            $table->string('retake_time'); //время пересдачи
            $table->string('room'); //аудитория
            $table->string('academic_year'); //учебный год
            $table->integer('half_year'); //полугодие
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_retake_schedules');
    }
};
