<?php

namespace App\Http\Controllers;

use App\Models\VideoConferenceFallback;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class VideoConferenceFallbackController extends Controller
{
    public function getLink($schedule_id, $currrent_date, $time_start, $time_end)
    {
        VideoConferenceFallback::where('date_lesson', '<', Carbon::now()->format('Y-m-d'))
            ->orWhere('time_end_lesson', '<', Carbon::now()->format('H:i'))
            ->update([
                'number_students' => 0,
                'schedule_id' => null,
                'date_lesson' => null,
                'time_start_lesson' => null,
                'time_end_lesson' => null,
                'teacher_id' => null,
                'student_id' => null,
                'is_activated' => 0
            ]);

        if (Auth::user()->hasRole('Teacher')) {
            $data = VideoConferenceFallback::where(function ($query) use ($schedule_id) {
                $query->where('schedule_id', '=', $schedule_id)
                    ->where('teacher_id', '=', Auth::user()->id);
            })
                ->orWhere('is_activated', '=', 0)
                ->select('id', 'link')
                ->first();

            if (!is_null($data)) {

                $videoConferenceFallback = VideoConferenceFallback::findOrFail($data->id);
                $videoConferenceFallback->schedule_id = $schedule_id;
                $videoConferenceFallback->date_lesson = $currrent_date;
                $videoConferenceFallback->time_start_lesson = $time_start;
                $videoConferenceFallback->time_end_lesson = $time_end;
                $videoConferenceFallback->teacher_id = Auth::user()->id;
                $videoConferenceFallback->is_activated = 1;
                $videoConferenceFallback->save();
            }
        }

        if (Auth::user()->hasRole('Student')) {
            $data = VideoConferenceFallback::where('schedule_id', '=', $schedule_id)->select('id', 'link')->first();

            if (!is_null($data)) {
                $videoConferenceFallback = VideoConferenceFallback::findOrFail($data->id);

                if ($videoConferenceFallback->number_students < 27) {

                    if (is_null($videoConferenceFallback->student_id)) {
                        $videoConferenceFallback->number_students = ++$videoConferenceFallback->number_students;
                        $videoConferenceFallback->student_id = json_encode(array(Auth::user()->id));
                    } else {
                        $current_student_ids = json_decode($videoConferenceFallback->student_id, true);

                        if (!in_array(Auth::user()->id, $current_student_ids)) {
                            $videoConferenceFallback->number_students = ++$videoConferenceFallback->number_students;
                            $students = array_merge($current_student_ids, array(Auth::user()->id));
                            $videoConferenceFallback->student_id = json_encode($students);
                        }
                    }

                    $videoConferenceFallback->update();
                } else {
                    $data = null;
                }
            }
        }

        return $data->link ?? null;
    }

    public function logout($id)
    {
        $videoConferenceFallback = VideoConferenceFallback::findOrFail($id);
        $videoConferenceFallback->schedule_id = null;
        $videoConferenceFallback->date_lesson = null;
        $videoConferenceFallback->time_start_lesson = null;
        $videoConferenceFallback->time_end_lesson = null;
        $videoConferenceFallback->teacher_id = null;
        $videoConferenceFallback->student_id = null;
        $videoConferenceFallback->is_activated = 0;
        $videoConferenceFallback->save();

        return redirect('/');
    }
}
