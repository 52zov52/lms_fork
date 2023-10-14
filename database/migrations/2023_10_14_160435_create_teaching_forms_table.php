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
        Schema::create('teaching_forms', function (Blueprint $table) {
            $table->id();
            $table->string('teaching_form_name', 50);
            $table->unsignedBigInteger('teaching_form_kis_id'); //Table name in IMBL BD dbo.Project_TeachingForm
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_forms');
    }
};
