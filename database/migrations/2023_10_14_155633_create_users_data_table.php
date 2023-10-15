<?php

use App\Models\Branch;
use App\Models\Document;
use App\Models\FamilyStatus;
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
        Schema::create('users_data', function (Blueprint $table) {
            $table->id();
            $table->string('last_name', 50);
            $table->string('first_name', 50);
            $table->string('middle_name', 50);
            $table->date('birthday');
            $table->string('birthplace');
            $table->integer('sex');

            $table->foreignIdFor(Document::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->string('passport_series', 10);
            $table->string('passport_number', 15);
            $table->date('passport_issue_date');
            $table->string('passport_issuing_authority', 100);
            $table->string('division_code', 10);
            $table->string('snils', 15);
            $table->string('inn', 15);

            $table->foreignIdFor(FamilyStatus::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->boolean('is_residence');
            $table->integer('citizenship');
            $table->integer('country');
            $table->string('last_name_genitive', 50);
            $table->string('first_name_genitive', 50);
            $table->string('middle_name_genitive', 50);
            $table->string('last_name_dative', 50);
            $table->string('first_name_dative', 50);
            $table->string('middle_name_dative', 50);

            $table->foreignIdFor(Branch::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unsignedBigInteger('user_data_kis_id'); //Table name in IMBL BD dbo.UserData_
            $table->unsignedBigInteger('user_kis_id'); //Table name in IMBL BD dbo.User
            $table->boolean('is_deleted');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_data');
    }
};
