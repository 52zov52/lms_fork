<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            UserSeeder::class,
            ContingentSeeder::class,
        ]);

        $this->outputSummary();
    }

    /**
     * Output summary of seeded test users.
     */
    private function outputSummary(): void
    {
        $this->command->info("\n>> Test accounts initialized (password: 'password'):");
        $this->command->line("   - Admin:    [Username: admin]    (Email: admin@i.ru, All permissions)");
        $this->command->line("   - Employee: [Username: employee] (Email: employee@i.ru, Contingent management)");
        $this->command->line("   - Teacher:  [Username: teacher]  (Email: teacher@i.ru, Journals & Schedules)");
        $this->command->line("   - Student:  [Username: student]  (Email: student@i.ru, Ticket: 100001)");
        $this->command->line("   - Students: [Username: student1 .. student6] (Groups: ИТ-24 К1, ЭК-23 К1, ЮР-22 К1)\n");
    }
}
