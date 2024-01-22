<?php

namespace App\Http\Controllers;

use App\Models\GroupStudent;
use Illuminate\Support\Facades\DB;

class ContingentController extends Controller
{
    public $groupId = [
        2406, 2409, 2397, 2410, 2393, 2414, 2415, 2416, 2404, 2417, 2407, 2411, 2412, 2413, 2361, 2402, 2362, 2405, 2392, 2352, 2353,
        2408, 2426, 2424, 2425, 2401, 2368, 2350, 2351, 2365, 2403, 2355, 2356, 2357, 2358, 2345, 2298, 2348, 2282, 2283, 2346, 2292,
        2349, 2301, 2302, 2347, 2288, 2289, 2290, 2291, 2303, 2304, 2385, 2337, 2422, 2339, 2308, 2398, 2222, 2340, 2395, 2341, 2306,
        2399, 2218, 2342, 2281, 2420, 2220, 2221, 2421, 2396, 2343, 2310, 2400, 2423, 2216, 2344, 2329, 2338, 2309, 2245, 2257, 2369,
        2371, 2372, 2319, 2376, 2323, 2261, 2205, 2377, 2324, 2325, 2256, 2208, 2375, 2327, 2260, 2259, 2378, 2320, 2248, 2192, 2379,
        2380, 2321, 2322, 2373, 2374, 2326, 2446, 2382, 2383, 2384, 2389, 2385, 2331, 2269, 2386, 2387, 2388, 2332, 2334, 2419, 2428
    ];

    public function getContingent($groupId): object
    {
        return GroupStudent::join('users_data', 'group_student.user_id', '=', 'users_data.user_id')
            ->select(
                'group_student.user_id',
                'group_student.id',
                'users_data.last_name',
                'users_data.first_name',
                'users_data.middle_name',
                DB::raw("(SELECT ticket_number FROM student_ticket_numbers WHERE user_id = group_student.user_id ORDER BY id DESC LIMIT 1) as ticket_number"),
                DB::raw("(SELECT states.name FROM student_state INNER JOIN states ON states.id = student_state.state_id WHERE student_state.user_id = group_student.user_id ORDER BY student_state.id DESC LIMIT 1) as state_name")
            )
            ->where('group_student.group_id', '=', $groupId)
            ->orderBy('users_data.last_name', 'asc')
            ->get();
    }
}
