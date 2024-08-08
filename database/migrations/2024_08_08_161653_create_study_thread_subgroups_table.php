<?php

use App\Models\StudyThread;
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
        Schema::create('study_thread_subgroups', function (Blueprint $table) {
            $table->id();
            $table->string('subgroup_name');

            $table->foreignIdFor(StudyThread::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.StudyThreadSubGroup
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_thread_subgroups');
    }
};
