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
        Schema::create('department_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_name', 50);
            $table->unsignedBigInteger('department_type_kis_id'); //Table name in IMBL BD dbo.DepartmentType
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_types');
    }
};
