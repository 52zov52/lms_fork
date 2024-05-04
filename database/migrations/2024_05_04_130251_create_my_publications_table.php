<?php

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
        Schema::create('my_publications', function (Blueprint $table) {
            $table->id();
            $table->integer('category');
            $table->integer('status');
            $table->string('title', 500);
            $table->string('output_data', 1500);
            $table->integer('year_publishing');
            $table->double('volume_full_in_printed_sheets');
            $table->double('volume_full_on_pages');
            $table->double('volume_author_in_printed_sheets');
            $table->double('volume_author_on_pages');
            $table->string('name_co_authors', 500)->nullable();
            $table->string('bibliographic_description', 1500);
            $table->string('file_path', 2048)->nullable();
            $table->boolean('is_university_affiliation')->default(false);

            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_publications');
    }
};
