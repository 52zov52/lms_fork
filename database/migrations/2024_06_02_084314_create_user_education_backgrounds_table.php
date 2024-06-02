<?php

use App\Models\Document;
use App\Models\LevelEducation;
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
        Schema::create('user_education_backgrounds', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('education_organization_id')->nullable();
            $table->foreign('education_organization_id', 'user_edu_education_organization_id_foreign')
                ->references('id')
                ->on('education_organizations')
                ->onDelete('cascade');

            $table->date('end_year')->nullable();

            $table->foreignIdFor(Document::class)
                ->constrained()
                ->cascadeOnDelete()
                ->nullable();

            $table->foreignIdFor(LevelEducation::class)
                ->constrained()
                ->cascadeOnDelete()
                ->nullable();

            $table->string('document_series')->nullable();
            $table->string('document_number')->nullable();
            $table->date('document_issue_date')->nullable();
            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.PrevEduData
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_education_backgrounds');
    }
};
