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
        Schema::create('student_family_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->boolean('is_married')->default(false);
            $table->date('marriage_date')->nullable();
            $table->boolean('is_divorced')->default(false);
            $table->date('divorce_date')->nullable();
            $table->boolean('is_widowed')->default(false);
            $table->date('previous_marriage_1_start')->nullable();
            $table->date('previous_marriage_1_end')->nullable();
            $table->date('previous_marriage_2_start')->nullable();
            $table->date('previous_marriage_2_end')->nullable();
            $table->date('previous_marriage_3_start')->nullable();
            $table->date('previous_marriage_3_end')->nullable();
            $table->date('previous_marriage_4_start')->nullable();
            $table->date('previous_marriage_4_end')->nullable();
            $table->date('previous_marriage_5_start')->nullable();
            $table->date('previous_marriage_5_end')->nullable();
            $table->boolean('is_spouse_studying')->default(false);
            $table->boolean('is_spouse_in_iubip')->default(false);
            $table->boolean('has_children')->default(false);
            $table->date('child_1_birth_date')->nullable();
            $table->date('child_2_birth_date')->nullable();
            $table->date('child_3_birth_date')->nullable();
            $table->date('child_4_birth_date')->nullable();
            $table->date('child_5_birth_date')->nullable();
            $table->boolean('is_single_parent')->default(false);
            $table->unsignedBigInteger('confirmiter_id')->nullable();
            $table->foreign('confirmiter_id')->references('id')->on('users')->onDelete('cascade');
            $table->char('confirmiter_signature', 64)->nullable();
            $table->char('user_signature', 64)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_family_statuses');
    }
};
