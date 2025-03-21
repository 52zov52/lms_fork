<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Законы (laws)
     * Постановления (decrees)
     * Приказы (orders)
     * Регламенты (regulations)
     * Стандарты (standards)
     * Инструкции (instructions)
     * Правила (rules)
     * Государственные стандарты (ГОСТ) (gost)
     * Санитарные нормы (sanitary_norms)
     * Технические регламенты (technical_regulations)
     */
    public function up(): void
    {
        Schema::create('regulatory_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type');
            $table->string('file_path', 2048);
            $table->text('filename');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regulatory_documents');
    }
};
