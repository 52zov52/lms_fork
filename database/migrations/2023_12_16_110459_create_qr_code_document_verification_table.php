<?php

use App\Models\User;
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
        Schema::create('qr_code_document_verifications', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class)
                ->cascadeOnDelete();

            $table->string('document_number')->nullable();
            $table->date('document_date')->nullable();
            $table->string('verification_code');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_code_document_verifications');
    }
};
