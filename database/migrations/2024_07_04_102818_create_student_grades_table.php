<?php

use App\Models\Bulletin;
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
        Schema::create('student_grades', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Bulletin::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();
            
            $table->integer('module_first_attendance')->nullable();
            $table->integer('module_first_test_assignment')->nullable();
            $table->integer('module_first_practical_assignment')->nullable();
            $table->integer('module_first_final_grade')->nullable();
            $table->integer('module_second_attendance')->nullable();
            $table->integer('module_second_test_assignment')->nullable();
            $table->integer('module_second_practical_assignment')->nullable();
            $table->integer('module_second_final_grade')->nullable();

            $table->integer('first_checkpoint')->nullable();
            $table->integer('second_checkpoint')->nullable();
            $table->integer('third_checkpoint')->nullable();
            $table->integer('fourth_checkpoint')->nullable();
            $table->integer('test_percentage_achieved')->nullable();
            $table->integer('current_progress')->nullable();
            $table->integer('answer_grade')->nullable();
            $table->integer('grade')->nullable();
            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.Project_IndividualProgress
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_grades');
    }
};
