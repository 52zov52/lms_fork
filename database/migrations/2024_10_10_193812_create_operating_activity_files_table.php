<?php

use App\Models\OperatingActivity;
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
        Schema::create('operating_activity_files', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(OperatingActivity::class)
                ->constrained()
                ->cascadeOnDelete();
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
        Schema::dropIfExists('operating_activity_files');
    }
};
