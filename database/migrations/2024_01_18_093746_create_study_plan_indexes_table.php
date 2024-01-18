<?php

use App\Models\LevelEducation;
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
        Schema::create('study_plan_indexes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('index');

            $table->foreignIdFor(LevelEducation::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unsignedBigInteger('study_plan_index_kis_id'); //Table name in IMBL BD dbo.Project_Cycle
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_plan_indexes');
    }
};
