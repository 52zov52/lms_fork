<?php

use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\Region;
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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Country::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->string('zip_code')->nullable();

            $table->foreignIdFor(Region::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(City::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(District::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->string('street')->nullable();
            $table->string('building')->nullable();
            $table->string('flat')->nullable();
            $table->string('phone')->nullable();

            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.Adress
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
