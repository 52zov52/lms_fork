<?php

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
        Schema::create('level_educations', function (Blueprint $table) {
            $table->id();
            $table->string('level_name', 50);
            $table->unsignedBigInteger('level_education_kis_id'); //Table name in IMBL BD dbo.Project_EducationLevel
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_educations');
    }
};
