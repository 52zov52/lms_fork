<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Document;
use App\Models\FamilyStatus;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $branch = Branch::firstOrCreate(
                ['id' => 1],
                [
                    'branch_name' => 'Головной ВУЗ',
                    'city' => 'Ростов-на-Дону',
                    'external_id' => 1,
                ]
            );

            $document = Document::firstOrCreate(
                ['id' => 1],
                [
                    'document_type' => 'Паспорт гражданина РФ',
                    'external_id' => 1,
                ]
            );

            $familyStatus = FamilyStatus::firstOrCreate(
                ['id' => 1],
                [
                    'status_name' => 'Холост / Не замужем',
                    'external_id' => 1,
                ]
            );

            $predictableUsers = [
                [
                    'username' => 'admin',
                    'email' => 'admin@i.ru',
                    'role' => 'Admin',
                    'secondary_role' => 'Employee',
                    'last_name' => 'Администратор',
                    'first_name' => 'Главный',
                    'middle_name' => 'Системный',
                ],
                [
                    'username' => 'employee',
                    'email' => 'employee@i.ru',
                    'role' => 'Employee',
                    'secondary_role' => null,
                    'last_name' => 'Сотрудников',
                    'first_name' => 'Сергей',
                    'middle_name' => 'Александрович',
                ],
                [
                    'username' => 'teacher',
                    'email' => 'teacher@i.ru',
                    'role' => 'Teacher',
                    'secondary_role' => null,
                    'last_name' => 'Преподавателев',
                    'first_name' => 'Иван',
                    'middle_name' => 'Сергеевич',
                ],
                [
                    'username' => 'student',
                    'email' => 'student@i.ru',
                    'role' => 'Student',
                    'secondary_role' => null,
                    'last_name' => 'Студентов',
                    'first_name' => 'Иван',
                    'middle_name' => 'Иванович',
                ],
            ];

            foreach ($predictableUsers as $u) {
                $user = User::firstOrCreate(
                    ['email' => $u['email']],
                    [
                        'name' => $u['username'],
                        'password' => Hash::make('password'),
                        'email_verified_at' => now(),
                    ]
                );

                $user->assignRole($u['role']);
                if (! empty($u['secondary_role'])) {
                    $user->assignRole($u['secondary_role']);
                }

                UserData::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'last_name' => $u['last_name'],
                        'first_name' => $u['first_name'],
                        'middle_name' => $u['middle_name'],
                        'birthday' => '2000-01-01',
                        'birthplace' => 'г. Ростов-на-Дону',
                        'sex' => 1,
                        'document_id' => $document->id,
                        'passport_series' => '6000',
                        'passport_number' => '123456',
                        'passport_issue_date' => '2020-01-01',
                        'passport_issuing_authority' => 'ГУ МВД',
                        'division_code' => '610-001',
                        'snils' => '000-000-000 00',
                        'inn' => '616400000000',
                        'family_status_id' => $familyStatus->id,
                        'is_residence' => true,
                        'citizenship' => 1,
                        'country' => 155,
                        'last_name_genitive' => $u['last_name'],
                        'first_name_genitive' => $u['first_name'],
                        'middle_name_genitive' => $u['middle_name'],
                        'last_name_dative' => $u['last_name'],
                        'first_name_dative' => $u['first_name'],
                        'middle_name_dative' => $u['middle_name'],
                        'branch_id' => $branch->id,
                        'external_user_data_id' => $user->id,
                        'external_user_id' => $user->id,
                        'is_deleted' => false,
                    ]
                );
            }
        });
    }
}
