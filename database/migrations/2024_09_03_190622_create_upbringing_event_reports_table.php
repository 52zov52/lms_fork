<?php

use App\Models\UpbringingEvent;
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
        Schema::create('upbringing_event_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(UpbringingEvent::class)
                ->constrained()
                ->cascadeOnDelete();
                
            $table->integer('status')->default(1);
            $table->string('file_path', 2048)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upbringing_event_reports');
    }
};
