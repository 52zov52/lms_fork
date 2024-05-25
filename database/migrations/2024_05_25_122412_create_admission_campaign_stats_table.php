<?php

use App\Models\AdmissionCampaign;
use App\Models\AdmissionCampaignDirection;
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
        Schema::create('admission_campaign_stats', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(AdmissionCampaignDirection::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(AdmissionCampaign::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('applications_certificate_count')->default(0);
            $table->integer('applications_diploma_bachelor_count')->default(0);
            $table->integer('applications_diploma_college_count')->default(0);
            $table->integer('contracts_certificate_count')->default(0);
            $table->integer('contracts_diploma_bachelor_count')->default(0);
            $table->integer('contracts_diploma_college_count')->default(0);
            $table->integer('paid_contracts_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_campaign_stats');
    }
};
