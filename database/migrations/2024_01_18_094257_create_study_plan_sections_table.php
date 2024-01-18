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
        Schema::create('study_plan_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('index_name');

            $table->foreignIdFor(LevelEducation::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unsignedBigInteger('study_plan_section_kis_id'); //Table name in IMBL BD dbo.Project_UnderCycle
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_plan_sections');
    }
};
