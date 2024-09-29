<?php

use App\Models\Address;
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
        Schema::create('address_user', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(Address::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('address_type');
            $table->string('external_id'); //Table name in IMBL BD dbo.UserAdress
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('address_user');
    }
};
