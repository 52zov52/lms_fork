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
        Schema::create('type_bulletins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_archive')->default(false);
            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.Project_TypeBulletin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_bulletins');
    }
};
