<?php

use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\Region;
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
        Schema::create('users_addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class)
            ->constrained()
            ->cascadeOnDelete()
            ->cascadeOnUpdate();

            $table->foreignIdFor(Country::class)
            ->nullable()
            ->constrained()
            ->cascadeOnDelete()
            ->cascadeOnUpdate();

            $table->string('zip_code')->nullable();

            $table->foreignIdFor(Region::class)
            ->nullable()
            ->constrained()
            ->cascadeOnDelete()
            ->cascadeOnUpdate();

            $table->foreignIdFor(City::class)
            ->nullable()
            ->constrained()
            ->cascadeOnDelete()
            ->cascadeOnUpdate();

            $table->foreignIdFor(District::class)
            ->nullable()
            ->constrained()
            ->cascadeOnDelete()
            ->cascadeOnUpdate();

            $table->string('street')->nullable();
            $table->string('building')->nullable();
            $table->string('flat')->nullable();
            $table->string('phone')->nullable();

            $table->string('users_address_kis_id'); //Table name in IMBL BD dbo.UserAdress
            $table->unsignedBigInteger('address_kis_id'); //Table name in IMBL BD dbo.Adress
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_addresses');
    }
};
