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
        Schema::create('operating_activities', function (Blueprint $table) {
            $table->id();
            $table->string('activity_direction');
            $table->string('task', 2500);
            $table->string('description', 3500)->nullable();
            $table->integer('status')->default(1);
            $table->string('file_path', 2048)->nullable();

            $table->foreignIdFor(User::class)
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operating_activities');
    }
};
