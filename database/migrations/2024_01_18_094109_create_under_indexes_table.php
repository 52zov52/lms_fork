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
        Schema::create('under_indexes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('index_name');
            $table->integer('sorting_order')->default(999);
            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.Project_UndeCycle2
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('under_indexes');
    }
};
