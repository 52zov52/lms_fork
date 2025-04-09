<?php

use App\Models\Schedule;
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
        Schema::create('video_conference_fallbacks', function (Blueprint $table) {
            $table->id();
            $table->string('room', 50);
            $table->string('link', 100);
            $table->integer('number_students')->default(0);
            $table->integer('total_number_students')->default(27);
            $table->foreignIdFor(Schedule::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->date('date_lesson')->nullable();
            $table->time('time_start_lesson')->nullable();
            $table->time('time_end_lesson')->nullable();
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
            $table->json('student_id')->nullable();
            $table->boolean('is_activated')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_conference_fallbacks');
    }
};
