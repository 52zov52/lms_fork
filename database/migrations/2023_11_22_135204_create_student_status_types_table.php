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
        Schema::create('student_status_types', function (Blueprint $table) {
            $table->id();
            $table->string('status_type_name');
            $table->unsignedBigInteger('student_status_type_kis_id'); //Table name in IMBL BD dbo.Student_StateType
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_status_types');
    }
};
