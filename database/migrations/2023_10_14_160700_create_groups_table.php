<?php

use App\Models\Branch;
use App\Models\StudyPlan;
use App\Models\WorkStudyPlan;
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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('group_name', 10);
            $table->integer('course');

            $table->date('year_admission')->nullable();
            $table->date('year_completion')->nullable();

            $table->foreignIdFor(StudyPlan::class)
                ->cascadeOnDelete();

            $table->foreignIdFor(WorkStudyPlan::class)
                ->cascadeOnDelete();

            $table->foreignIdFor(Branch::class)
                ->cascadeOnDelete();

            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.Project_Group
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
