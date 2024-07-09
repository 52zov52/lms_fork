<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentGrades extends Model
{
    use HasFactory;

    protected $fillable = [
        'bulletin_id',
        'user_id',
        'rating_attendance',
        'rating_test_assignment',
        'rating_practical_assignment',
        'rating_final_grade',
        'module_first_attendance',
        'module_first_test_assignment',
        'module_first_practical_assignment',
        'module_first_final_grade',
        'module_second_attendance',
        'module_second_test_assignment',
        'module_second_practical_assignment',
        'module_second_final_grade',
        'first_checkpoint',
        'second_checkpoint',
        'third_checkpoint',
        'fourth_checkpoint',
        'test_percentage_achieved',
        'current_progress',
        'answer_grade',
        'grade',
        'external_id'
    ];
}
