<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\DepartmentPosition;
use App\Models\DepartmentType;
use App\Models\Document;
use App\Models\EmployeeCategory;
use App\Models\FamilyStatus;
use App\Models\Group;
use App\Models\LevelEducation;
use App\Models\Position;
use App\Models\Qualification;
use App\Models\Speciality;
use App\Models\StudyPlan;
use App\Models\TeachingForm;
use App\Models\User;
use App\Models\UserData;
use App\Models\WorkStudyPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ContingentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $branch = Branch::firstOrCreate(
                ['id' => 1],
                ['branch_name' => 'Головной ВУЗ', 'city' => 'Ростов-на-Дону', 'external_id' => 1]
            );

            $document = Document::firstOrCreate(
                ['id' => 1],
                ['document_type' => 'Паспорт гражданина РФ', 'external_id' => 1]
            );

            $familyStatus = FamilyStatus::firstOrCreate(
                ['id' => 1],
                ['status_name' => 'Холост / Не замужем', 'external_id' => 1]
            );

            $teachingForm = TeachingForm::firstOrCreate(
                ['id' => 1],
                ['teaching_form_name' => 'Очная', 'external_id' => 1]
            );

            $levelEducation = LevelEducation::firstOrCreate(
                ['id' => 1],
                ['level_name' => 'Бакалавриат', 'external_id' => 1]
            );

            $qualification = Qualification::firstOrCreate(
                ['id' => 1],
                ['qualification_name' => 'Бакалавр', 'external_id' => 1]
            );

            $category = EmployeeCategory::firstOrCreate(
                ['id' => 1],
                ['category_name' => 'ППС', 'external_id' => 1]
            );

            $position = Position::firstOrCreate(
                ['id' => 1],
                ['position_title' => 'Заведующий кафедрой', 'employee_category_id' => $category->id, 'external_id' => 1]
            );

            $depType = DepartmentType::firstOrCreate(
                ['id' => 1],
                ['type_name' => 'Кафедра', 'external_id' => 1]
            );

            $department = Department::firstOrCreate(
                ['id' => 1],
                ['department_name' => 'Информационные технологии', 'department_type_id' => $depType->id, 'external_id' => 1]
            );

            $depPos = DepartmentPosition::firstOrCreate(
                ['id' => 1],
                ['department_id' => $department->id, 'position_id' => $position->id, 'external_id' => 1]
            );

            $speciality = Speciality::firstOrCreate(
                ['id' => 1],
                [
                    'speciality_name' => 'Информационные системы и технологии',
                    'specialty_code' => '09.03.02',
                    'level_education_id' => $levelEducation->id,
                    'qualification_id' => $qualification->id,
                    'department_position_id' => $depPos->id,
                    'external_id' => 1,
                ]
            );

            $studyPlan = StudyPlan::firstOrCreate(
                ['id' => 1],
                [
                    'plan_name' => 'Учебный план ИСТ-2024',
                    'speciality_id' => $speciality->id,
                    'teaching_form_id' => $teachingForm->id,
                    'level_education_id' => $levelEducation->id,
                    'qualification_id' => $qualification->id,
                    'number_semesters' => 8,
                    'number_years' => 4,
                    'number_months' => 0,
                    'start_date' => '2024-09-01 00:00:00',
                    'end_date' => '2028-06-30 00:00:00',
                    'creation_date' => '2024-06-01 00:00:00',
                    'approval_date' => '2024-06-15 00:00:00',
                    'external_id' => 1,
                ]
            );

            $workStudyPlan = WorkStudyPlan::firstOrCreate(
                ['id' => 1],
                [
                    'plan_name' => 'РУП ИСТ-2024',
                    'speciality_id' => $speciality->id,
                    'teaching_form_id' => $teachingForm->id,
                    'level_education_id' => $levelEducation->id,
                    'branch_id' => $branch->id,
                    'study_plan_id' => $studyPlan->id,
                    'creation_date' => '2024-06-01 00:00:00',
                    'approval_date' => '2024-06-15 00:00:00',
                    'external_id' => 1,
                ]
            );

            // Test Groups
            $groupsData = [
                ['name' => 'ИТ-24 К1', 'course' => 1, 'admission' => '2024-09-01', 'completion' => '2028-06-30', 'ext_id' => 101],
                ['name' => 'ЭК-23 К1', 'course' => 2, 'admission' => '2023-09-01', 'completion' => '2027-06-30', 'ext_id' => 102],
                ['name' => 'ЮР-22 К1', 'course' => 3, 'admission' => '2022-09-01', 'completion' => '2026-06-30', 'ext_id' => 103],
            ];

            $groups = [];
            foreach ($groupsData as $g) {
                $groups[$g['name']] = Group::firstOrCreate(
                    ['group_name' => $g['name']],
                    [
                        'course' => $g['course'],
                        'year_admission' => $g['admission'],
                        'year_completion' => $g['completion'],
                        'study_plan_id' => $studyPlan->id,
                        'work_study_plan_id' => $workStudyPlan->id,
                        'branch_id' => $branch->id,
                        'external_id' => $g['ext_id'],
                    ]
                );
            }

            // Test Students
            $studentsData = [
                ['username' => 'student1', 'last_name' => 'Иванов', 'first_name' => 'Иван', 'middle_name' => 'Иванович', 'ticket' => 100001, 'group' => 'ИТ-24 К1', 'email' => 'student1@i.ru'],
                ['username' => 'student2', 'last_name' => 'Петров', 'first_name' => 'Петр', 'middle_name' => 'Петрович', 'ticket' => 100002, 'group' => 'ИТ-24 К1', 'email' => 'student2@i.ru'],
                ['username' => 'student3', 'last_name' => 'Сидоров', 'first_name' => 'Алексей', 'middle_name' => 'Сергеевич', 'ticket' => 100003, 'group' => 'ИТ-24 К1', 'email' => 'student3@i.ru'],
                ['username' => 'student4', 'last_name' => 'Смирнова', 'first_name' => 'Анна', 'middle_name' => 'Дмитриевна', 'ticket' => 100004, 'group' => 'ЭК-23 К1', 'email' => 'student4@i.ru'],
                ['username' => 'student5', 'last_name' => 'Кузнецов', 'first_name' => 'Максим', 'middle_name' => 'Олегович', 'ticket' => 100005, 'group' => 'ЭК-23 К1', 'email' => 'student5@i.ru'],
                ['username' => 'student6', 'last_name' => 'Попова', 'first_name' => 'Елена', 'middle_name' => 'Владимировна', 'ticket' => 100006, 'group' => 'ЮР-22 К1', 'email' => 'student6@i.ru'],
            ];

            foreach ($studentsData as $st) {
                $user = User::firstOrCreate(
                    ['email' => $st['email']],
                    [
                        'name' => $st['username'],
                        'password' => Hash::make('password'),
                        'email_verified_at' => now(),
                    ]
                );

                $user->assignRole('Student');

                UserData::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'last_name' => $st['last_name'],
                        'first_name' => $st['first_name'],
                        'middle_name' => $st['middle_name'],
                        'birthday' => '2004-05-15',
                        'birthplace' => 'г. Ростов-на-Дону',
                        'sex' => 1,
                        'document_id' => $document->id,
                        'passport_series' => '6000',
                        'passport_number' => (string) (200000 + $st['ticket'] % 1000),
                        'passport_issue_date' => '2020-01-01',
                        'passport_issuing_authority' => 'ГУ МВД',
                        'division_code' => '610-001',
                        'snils' => '000-000-000 00',
                        'inn' => '616400000000',
                        'family_status_id' => $familyStatus->id,
                        'is_residence' => true,
                        'citizenship' => 1,
                        'country' => 155,
                        'last_name_genitive' => $st['last_name'],
                        'first_name_genitive' => $st['first_name'],
                        'middle_name_genitive' => $st['middle_name'],
                        'last_name_dative' => $st['last_name'],
                        'first_name_dative' => $st['first_name'],
                        'middle_name_dative' => $st['middle_name'],
                        'branch_id' => $branch->id,
                        'external_user_data_id' => $user->id,
                        'external_user_id' => $user->id,
                        'is_deleted' => false,
                    ]
                );

                // Student Ticket Number
                DB::table('student_ticket_numbers')->updateOrInsert(
                    ['user_id' => $user->id],
                    [
                        'ticket_number' => $st['ticket'],
                        'external_id' => $st['ticket'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                // Group Student link
                $targetGroup = $groups[$st['group']];
                DB::table('group_student')->updateOrInsert(
                    ['group_id' => $targetGroup->id, 'user_id' => $user->id],
                    [
                        'external_id' => $user->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        });
    }
}
