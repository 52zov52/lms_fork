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
        Schema::create('decisions_rectorate_and_council', function (Blueprint $table) {
            $table->id();
            $table->string('position');
            $table->string('task');
            $table->string('description', 1500)->nullable();
            $table->string('due_date');
            $table->integer('status')->default(1);
            $table->integer('category')->default(1);
            $table->string('file_path', 2048)->nullable();

            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decisions_rectorate_and_council');
    }
};
