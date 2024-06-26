<?php

use App\Models\Group;
use App\Models\Order;
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
        Schema::create('students_academic_leaves', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(Group::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(Order::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->date('academic_leave_start_date')->nullable();
            $table->date('academic_leave_end_date')->nullable();
            $table->string('description', 1500)->nullable();
            $table->boolean('is_academic_leave')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students_academic_leaves');
    }
};
