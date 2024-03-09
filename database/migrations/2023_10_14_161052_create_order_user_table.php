<?php

use App\Models\Order;
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
        Schema::create('order_user', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Order::class)
                ->cascadeOnDelete();

            $table->foreignIdFor(User::class)
                ->cascadeOnDelete();

            $table->string('notes')->nullable();
            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.Student_DocMovePerson
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_user');
    }
};
