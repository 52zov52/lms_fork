<?php

use App\Models\Department;
use App\Models\Discipline;
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
        Schema::create('chair_discipline', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Department::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(Discipline::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('year');
            $table->integer('semester');
            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.Project_ChairDiscipline
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chair_discipline');
    }
};
