<?php

use App\Models\Discipline;
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
        Schema::create('consultation_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete(); //ИД преподавателя
            $table->foreignIdFor(Discipline::class)
                ->constrained()
                ->cascadeOnDelete(); //ИД дисциплины
            $table->date('consultation_date'); //дата пересдачи
            $table->string('consultation_time'); //время пересдачи
            $table->string('room'); //аудитория
            $table->json('groups'); // список группы
            $table->string('academic_year'); //учебный год
            $table->integer('half_year'); //полугодие
            $table->string('description')->nullable(); //описание или комментарий
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_schedules');
    }
};
