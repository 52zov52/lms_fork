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
        Schema::create('family_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status_name', 50);
            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.FamilyMariedType
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_statuses');
    }
};
