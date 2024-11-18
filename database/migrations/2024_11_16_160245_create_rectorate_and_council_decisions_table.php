<?php

use App\Models\OperatingActivity;
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
        Schema::create('rectorate_and_council_decisions', function (Blueprint $table) {
            $table->id();
            $table->date('date_decision')->nullable();
            $table->string('number_decision', 50)->nullable();
            $table->string('position');
            $table->string('task', 1500);
            $table->date('due_date');
            $table->integer('status')->default(1);
            $table->integer('category')->default(1);

            $table->foreignIdFor(OperatingActivity::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('executor_id')->nullable();
            $table->foreign('executor_id')
                ->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('rectorate_and_council_decisions');
    }
};
