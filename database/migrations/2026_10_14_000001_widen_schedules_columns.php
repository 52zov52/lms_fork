<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // group был varchar(10), teacher - varchar(50): длинные названия групп / ФИО из DBF не помещались
        DB::statement('ALTER TABLE `schedules`
            MODIFY `group` VARCHAR(100) NOT NULL,
            MODIFY `course` VARCHAR(50) NOT NULL,
            MODIFY `lesson_number` VARCHAR(50) NOT NULL,
            MODIFY `day` VARCHAR(50) NOT NULL,
            MODIFY `week` VARCHAR(50) NOT NULL,
            MODIFY `date` VARCHAR(50) NOT NULL,
            MODIFY `time` VARCHAR(50) NOT NULL,
            MODIFY `teacher` VARCHAR(255) NOT NULL');
    }

    public function down(): void
    {
        // откат не делаем: сужение столбцов может обрезать данные
    }
};
