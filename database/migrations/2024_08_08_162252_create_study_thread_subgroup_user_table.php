<?php

use App\Models\StudyThreadSubGroup;
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
        Schema::create('study_thread_subgroup_user', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('study_thread_subgroup_id');
            $table->foreign('study_thread_subgroup_id')
                ->references('id')
                ->on('study_thread_subgroups')
                ->onDelete('cascade');

            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();
                
            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.StudyThread_Trainer
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_thread_subgroup_user');
    }
};
