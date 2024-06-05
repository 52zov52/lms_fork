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
        Schema::create('type_educations', function (Blueprint $table) {
            $table->id();
            $table->string('type_name', 150);
            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.EducationalLevel
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_educations');
    }
};
