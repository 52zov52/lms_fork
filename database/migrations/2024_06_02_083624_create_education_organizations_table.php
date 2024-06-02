<?php

use App\Models\City;
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
        Schema::create('education_organizations', function (Blueprint $table) {
            $table->id();

            $table->string('name', 1500);

            $table->unsignedBigInteger('education_organization_type_id')->nullable();
            $table->foreign('education_organization_type_id', 'edu_org_education_organization_type_id_foreign')
                ->references('id')
                ->on('education_organization_types')
                ->onDelete('cascade');

            $table->foreignIdFor(City::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.Institutes

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_organizations');
    }
};
