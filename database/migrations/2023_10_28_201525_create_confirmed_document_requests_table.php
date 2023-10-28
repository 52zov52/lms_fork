<?php

use App\Models\DocumentOrderRequest;
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
        Schema::create('confirmed_document_requests', function (Blueprint $table) {
            $table->id();
            $table->string('last_name', 50);
            $table->string('first_name', 50);
            $table->string('middle_name', 50);
            $table->integer('sex');
            $table->integer('course');
            $table->string('teaching_form_name', 50);
            $table->string('speciality_name', 100);
            $table->string('specialty_code', 50);
            $table->string('orders', 5000);
            $table->string('training_period', 50);

            $table->foreignIdFor(DocumentOrderRequest::class)
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
        Schema::dropIfExists('confirmed_document_requests');
    }
};
