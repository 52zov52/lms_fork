<?php

use App\Models\ActivityDirection;
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
        Schema::create('user_activity_directions', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class)
                ->cascadeOnDelete();

            $table->foreignIdFor(ActivityDirection::class)
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
        Schema::dropIfExists('user_activity_directions');
    }
};
