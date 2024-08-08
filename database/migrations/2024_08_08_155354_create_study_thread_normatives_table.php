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
        Schema::create('study_thread_normatives', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('hour_weight');
            $table->float('person_weight');
            $table->float('day_weight');
            $table->integer('edge');
            $table->integer('less');
            $table->integer('more');
            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.StudyThread_Normative
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_thread_normatives');
    }
};
