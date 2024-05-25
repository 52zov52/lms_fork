<?php

use App\Models\AdmissionCampaign;
use App\Models\LevelEducation;
use App\Models\TeachingForm;
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
        Schema::create('admission_campaign_directions', function (Blueprint $table) {
            $table->id();
            $table->string('speciality_code', 50);
            $table->string('speciality_name');
            $table->string('speciality_profile')->nullable();

            $table->foreignIdFor(LevelEducation::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(TeachingForm::class)
                ->constrained()
                ->cascadeOnDelete();
            
            $table->integer('funding_type')->default(1);

            $table->foreignIdFor(AdmissionCampaign::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_campaign_directions');
    }
};
