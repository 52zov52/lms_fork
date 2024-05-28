<?php

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
        Schema::create('admission_campaign_stats_masters_degrees', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('admission_campaign_direction_id')->nullable();
            $table->foreign('admission_campaign_direction_id', 'mas_admission_campaign_direction_id_foreign')
                ->references('id')
                ->on('admission_campaign_directions')
                ->onDelete('cascade');

            $table->unsignedBigInteger('admission_campaign_id')->nullable();
            $table->foreign('admission_campaign_id', 'mas_admission_campaign_id_foreign')
                ->references('id')
                ->on('admission_campaigns')
                ->onDelete('cascade');

            $table->integer('applications_count')->default(0);
            $table->integer('contracts_count')->default(0);
            $table->integer('paid_contracts_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_campaign_stats_masters_degrees');
    }
};
