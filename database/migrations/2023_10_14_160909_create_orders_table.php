<?php

use App\Models\OrderType;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(OrderType::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->string('order_number');
            $table->date('order_date');
            $table->string('description', 1500)->nullable();
            $table->unsignedBigInteger('external_id'); //Table name in IMBL BD dbo.DocumentParams
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
