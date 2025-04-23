<?php

use App\Models\AssignmentComment;
use App\Models\TeacherAssignment;
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
        Schema::create('assignment_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TeacherAssignment::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(AssignmentComment::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->string('path_to_file', 9216);
            $table->string('original_filename');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_attachments');
    }
};
