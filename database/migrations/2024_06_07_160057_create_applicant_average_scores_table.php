<?php

use App\Models\AdmissionCampaign;
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
        Schema::create('applicant_average_scores', function (Blueprint $table) {
            $table->id();

            $table->string('last_name', 50);
            $table->string('first_name', 50);
            $table->string('middle_name', 50)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();

            $table->foreignIdFor(User::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('threes_count')->default(0);
            $table->integer('fours_count')->default(0);
            $table->integer('fives_count')->default(0);
            $table->double('average_score')->default(0);
            $table->integer('completed_classes_count')->default(9);

            $table->foreignIdFor(AdmissionCampaign::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('admission_campaign_direction_id')->nullable();
            $table->foreign('admission_campaign_direction_id', 'aas_admission_campaign_direction_id_foreign')
                ->references('id')
                ->on('admission_campaign_directions')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_average_scores');
    }
};
