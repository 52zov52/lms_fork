<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Законы (Laws)
     * Постановления (Decrees)
     * Приказы (Orders)
     * Регламенты (Regulations)
     * Стандарты (Standards)
     * Инструкции (Instructions)
     * Правила (Rules)
     * Государственные стандарты (ГОСТ) (GOST)
     * Санитарные нормы (Sanitary Norms)
     * Технические регламенты (Technical Regulations)
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
