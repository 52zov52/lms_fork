<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ContingentReportController extends Controller
{
    public $groupId = [
        2406, 2409, 2397, 2410, 2393, 2414, 2415, 2416, 2404, 2417, 2407, 2411, 2412, 2413, 2361, 2402, 2362, 2405, 2392, 2352, 2353,
        2408, 2426, 2424, 2425, 2401, 2368, 2350, 2351, 2365, 2403, 2355, 2356, 2357, 2358, 2345, 2298, 2348, 2282, 2283, 2346, 2292,
        2349, 2301, 2302, 2347, 2288, 2289, 2290, 2291, 2303, 2304, 2385, 2337, 2422, 2339, 2308, 2398, 2222, 2340, 2395, 2341, 2306,
        2399, 2218, 2342, 2281, 2420, 2220, 2221, 2421, 2396, 2343, 2310, 2400, 2423, 2216, 2344, 2329, 2338, 2309, 2245, 2257, 2369,
        2371, 2372, 2319, 2376, 2323, 2261, 2205, 2377, 2324, 2325, 2256, 2208, 2375, 2327, 2260, 2259, 2378, 2320, 2248, 2192, 2379,
        2380, 2321, 2322, 2373, 2374, 2326, 2446, 2382, 2383, 2384, 2389, 2385, 2331, 2269, 2386, 2387, 2388, 2332, 2334

    ];

    public $firstCourseGroupId = array();
    public $secondCourseGroupId = array();
    public $thirdCourseGroupId = array();
    public $fourthCourseGroupId = array();
    public $fiveCourseGroupId = array();
    public $sixCourseGroupId = array();

    public $firstYearStudents = 0, $secondYearStudents = 0, $thirdYearStudents = 0, $fourthYearStudents = 0, $fiveYearStudents = 0, $sixYearStudents = 0;
    public $totalStudentsOnCourses = 0;

    public $collegeGroupId = array();
    public $bachelorGroupId = array();
    public $masterGroupId = array();

    public $collegeFullTimeGroupId = array();

    public $bachelorFullTimeGroupId = array();
    public $bachelorPartTimeGroupId = array();
    public $bachelorFullPartTimeGroupId = array();

    public $masterFullTimeGroupId = array();
    public $masterPartTimeGroupId = array();
    public $masterFullPartTimeGroupId = array();

    public function generateContingentReport()
    {
        /*
        $data = Group::select('groups.id', 'groups.course', 'specialities.level_education_id', 'study_plans.teaching_form_id')
            ->join('study_plans', 'groups.study_plan_id', '=', 'study_plans.id')
            ->join('specialities', 'study_plans.speciality_id', '=', 'specialities.id')
            ->whereIn('groups.id', $this->groupId)
            ->get();
        */

        $data = DB::table('groups')->select(
            'groups.group_name',
            'groups.course',
            DB::raw("(SELECT COUNT(group_student.id) FROM group_student INNER JOIN groups ON group_student.group_id = groups.id WHERE group_student.group_id = groups.id) as count_students")
        )
            ->whereIn('groups.id', $this->groupId)
            ->get();

        $results = DB::table('groups')
            ->join('group_student', 'groups.id', '=', 'group_student.group_id')
            ->join('study_plans', 'groups.study_plan_id', '=', 'study_plans.id')
            ->join('specialities', 'study_plans.speciality_id', '=', 'specialities.id')
            ->select('groups.id', 'groups.group_name', 'groups.course', DB::raw('COUNT(group_student.user_id) as student_count'), 'specialities.level_education_id', 'study_plans.teaching_form_id')
            ->whereIn('groups.id', $this->groupId)
            ->groupBy('groups.id', 'groups.group_name', 'groups.course')
            ->get();

        dd($results);
        die();
        /*
            2 - СПО
            6 - Бак
            7 - Маг 
        */
        foreach ($data as $key => $value) {
            switch ($value->level_education_id) {
                case 2:
                    $this->collegeGroupId[] = $value->id;

                    if ($value->teaching_form_id == 1) {
                        $this->collegeFullTimeGroupId = $value->id;
                    } elseif ($value->teaching_form_id == 2) {
                    } else {
                    }
                    break;
                case 6:
                    $this->bachelorGroupId[] = $value->id;

                    if ($value->teaching_form_id == 1) {
                        $this->bachelorFullTimeGroupId = $value->id;
                    } elseif ($value->teaching_form_id == 2) {
                        $this->bachelorPartTimeGroupId = $value->id;
                    } else {
                        $this->bachelorFullPartTimeGroupId = $value->id;
                    }
                    break;
                case 7:
                    $this->masterGroupId[] = $value->id;

                    if ($value->teaching_form_id == 1) {
                        $this->masterFullTimeGroupId = $value->id;
                    } elseif ($value->teaching_form_id == 2) {
                        $this->masterPartTimeGroupId = $value->id;
                    } else {
                        $this->masterFullPartTimeGroupId = $value->id;
                    }
                    break;
                default:
                    break;
            }
        }



        foreach ($this->collegeGroupId as $key => $value) {
            # code...
        }

        $countStudentsInCollege = GroupStudent::whereIn('group_id', $this->collegeGroupId)->count();
        $countStudentsInBachelor = GroupStudent::whereIn('group_id', $this->bachelorGroupId)->count();
        $countStudentsInMaster = GroupStudent::whereIn('group_id', $this->masterGroupId)->count();

        $total = $countStudentsInCollege + $countStudentsInBachelor + $countStudentsInMaster;

        echo "Колледж: $countStudentsInCollege, Бак: $countStudentsInBachelor, Маг: $countStudentsInMaster, Всего: $total";

        /*
        $data = Group::whereIn('id', $this->groupId)->get();

        foreach ($data as $value) {
            switch ($value->course) {
                case 1:
                    $this->firstCourseGroupId[] = $value->id;
                    break;
                case 2:
                    $this->secondCourseGroupId[] = $value->id;
                    break;
                case 3:
                    $this->thirdCourseGroupId[] = $value->id;
                    break;
                case 4:
                    $this->fourthCourseGroupId[] = $value->id;
                    break;
                case 5:
                    $this->fiveCourseGroupId[] = $value->id;
                    break;
                case 6:
                    $this->sixCourseGroupId[] = $value->id;
                    break;
                default:
                    break;
            }
        }

        $this->firstYearStudents = GroupStudent::whereIn('group_id', $this->firstCourseGroupId)->count();
        $this->secondYearStudents = GroupStudent::whereIn('group_id', $this->secondCourseGroupId)->count();
        $this->thirdYearStudents = GroupStudent::whereIn('group_id', $this->thirdCourseGroupId)->count();
        $this->fourthYearStudents = GroupStudent::whereIn('group_id', $this->fourthCourseGroupId)->count();
        $this->fiveYearStudents = GroupStudent::whereIn('group_id', $this->fiveCourseGroupId)->count();
        $this->sixYearStudents = GroupStudent::whereIn('group_id', $this->sixCourseGroupId)->count();

        $this->totalStudentsOnCourses = $this->firstYearStudents + $this->secondYearStudents + $this->thirdYearStudents + $this->fourthYearStudents + $this->fiveYearStudents + $this->sixYearStudents;

        dd($this->totalStudentsOnCourses);die();
        */
    }
}
