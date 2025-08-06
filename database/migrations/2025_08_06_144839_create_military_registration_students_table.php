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
        Schema::create('military_registration_students', function (Blueprint $table) {
            $table->id();
            $table->string('reserve_category', 10)->nullable();
            $table->string('military_rank', 50)->nullable();
            $table->string('military_occupation_profile', 50)->nullable();
            $table->string('full_military_specialty_code', 50);
            $table->string('fitness_category', 10);
            $table->string('military_commissariat_name');
            $table->string('military_registration_type_general')->nullable();
            $table->string('military_registration_type_special')->nullable();
            $table->string('military_document');
            $table->string('document_series', 50);
            $table->string('document_number', 50);
            $table->string('issued_by');
            $table->date('issue_date');
            $table->foreignIdFor(User::class);
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('military_registration_students');
    }
};
