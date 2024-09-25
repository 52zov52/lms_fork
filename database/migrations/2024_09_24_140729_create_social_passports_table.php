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
        Schema::create('social_passports', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->string('last_name', 50);
            $table->string('first_name', 50);
            $table->string('middle_name', 50)->nullable();
            $table->date('birthday');
            $table->string('registered_address', 1500);
            $table->string('housing_conditions', 20);
            $table->json('cohabitants');
            $table->string('family_composition', 20);
            $table->integer('number_of_children')->default(1);
            $table->string('father_last_name', 50)->nullable();
            $table->string('father_first_name', 50)->nullable();
            $table->string('father_middle_name', 50)->nullable();
            $table->string('father_workplace')->nullable();
            $table->string('father_phone', 20)->nullable();
            $table->string('mother_last_name', 50)->nullable();
            $table->string('mother_first_name', 50)->nullable();
            $table->string('mother_middle_name', 50)->nullable();
            $table->string('mother_workplace')->nullable();
            $table->string('mother_phone', 20)->nullable();
            $table->boolean('father_disability')->default(false);
            $table->boolean('mother_disability')->default(false);
            $table->json('child_categories')->nullable();
            $table->integer('questionnaire_status')->default(0);
            $table->unsignedBigInteger('confirmiter_id')->nullable();
            $table->foreign('confirmiter_id')->references('id')->on('users')->onDelete('cascade');
            $table->char('confirmiter_signature', 64)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_passports');
    }
};
