<?php

use App\Models\OperatingActivityTaskList;
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
        Schema::create('operating_activity_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_name');
            $table->date('due_date');
            $table->string('description', 5000)->nullable();
            $table->integer('status')->default(1);
            $table->integer('order')->default(99);
            $table->string('file_path', 2048)->nullable();

            $table->foreignIdFor(OperatingActivityTaskList::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

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
        Schema::dropIfExists('operating_activity_tasks');
    }
};
