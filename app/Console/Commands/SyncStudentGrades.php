<?php

namespace App\Console\Commands;

use App\Models\StudentGrades;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SyncStudentGrades extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-student-grades';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync student grades daily';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        StudentGrades::truncate();

        $filePath = Storage::disk('public')->path('student_grades/student_grades_data.txt');
        $file = fopen($filePath, 'r');
        while(!feof($file)) {
            $line = fgets($file);
            if (trim($line) === '') {
                continue;
            }
            
            $values = explode('|', $line);
            StudentGrades::create([
                'bulletin_id' => $values[0],
                'user_id' => $values[1],
                'rating_attendance' => ($values[2] == "None" ? null : $values[2]),
                'rating_test_assignment' => ($values[3] == "None" ? null : $values[3]),
                'rating_practical_assignment' => ($values[4] == "None" ? null : $values[4]),
                'rating_final_grade' => ($values[5] == "None" ? null : $values[5]),
                'module_first_attendance' => ($values[6] == "None" ? null : $values[6]),
                'module_first_test_assignment' => ($values[7] == "None" ? null : $values[7]),
                'module_first_practical_assignment' => ($values[8] == "None" ? null : $values[8]),
                'module_first_final_grade' => ($values[9] == "None" ? null : $values[9]),
                'module_second_attendance' => ($values[10] == "None" ? null : $values[10]),
                'module_second_test_assignment' => ($values[11] == "None" ? null : $values[1]),
                'module_second_practical_assignment' => ($values[12] == "None" ? null : $values[12]),
                'module_second_final_grade' => ($values[13] == "None" ? null : $values[13]),
                'first_checkpoint' => ($values[14] == "None" ? null : $values[14]),
                'second_checkpoint' => ($values[15] == "None" ? null : $values[15]),
                'third_checkpoint' => ($values[16] == "None" ? null : $values[16]),
                'fourth_checkpoint' => ($values[17] == "None" ? null : $values[17]),
                'test_percentage_achieved' => ($values[18] == "None" ? null : $values[18]),
                'current_progress' => ($values[19] == "None" ? null : $values[19]),
                'answer_grade' => ($values[20] == "None" ? null : $values[20]),
                'grade' => ($values[21] == "None" ? null : $values[21]),
                'external_id' => $values[22],
                'created_at' => $values[23],
                'updated_at' => trim($values[24]),
            ]);
        }

        fclose($file);

        $this->info('Successfully sync student grades.');
    }
}
