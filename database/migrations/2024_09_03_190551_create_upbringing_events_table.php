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
        Schema::create('upbringing_events', function (Blueprint $table) {
            $table->id();
            $table->integer('category');
            $table->string('name', 1500);
            $table->text('description', 1500)->nullable();
            $table->date('date');
            $table->string('academic_year');
            $table->integer('half_year');

            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upbringing_events');
    }
};
