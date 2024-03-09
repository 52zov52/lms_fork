<?php

use App\Models\DepartmentPosition;
use App\Models\Speciality;
use App\Models\Status;
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
        Schema::create('user_status', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(Status::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(Speciality::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('department_position_id');
            $table->foreign('department_position_id')->references('id')->on('department_position')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.UserStatus
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_status');
    }
};
