<?php

use App\Models\Discipline;
use App\Models\Group;
use App\Models\StudyThreadNormative;
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
        Schema::create('async_interact_chats', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
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
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->string('role');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('async_interact_chats');
    }
};
