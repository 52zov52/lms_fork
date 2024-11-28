<?php

use App\Models\StudentGrades;
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
        Schema::create('student_retake_bulletins', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(StudentGrades::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->integer('retake_bulletin_number');
            $table->foreignIdFor(TypeBulletin::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->datetime('opening_date');
            $table->datetime('closing_date');
            $table->string('pay_number', 50)->nullable();
            $table->date('pay_date')->nullable();
            $table->integer('grade')->nullable();
            $table->unsignedBigInteger('teacher_id');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.Project_IndividualAppointmentCard
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_retake_bulletins');
    }
};
