<?php

use App\Models\AsyncInteractChatComment;
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
        Schema::create('async_interact_chat_files', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(AsyncInteractChatComment::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->string('path_to_file', 9216);
            $table->string('original_filename');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('async_interact_chat_files');
    }
};
