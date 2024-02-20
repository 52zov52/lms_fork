<?php

namespace App\Livewire;

use App\Http\Controllers\ContingentController;
use App\Models\CourseTeacher;
use App\Models\EJournal as ModelsEJournal;
use Carbon\Carbon;
use Livewire\Component;

class EJournal extends Component
{
    public $isAlertVisible = false;
    public $courseTeacherId = 0;
    public $status = array();

    public function mount($courseTeacherId)
    {
        $this->courseTeacherId = $courseTeacherId;
    }

    public function render()
    {
        $currentData = Carbon::now()->format('d.m.Y');
        $currentTime = $this->getLessonTime(Carbon::now()->format('H:i'));

        $courseTeacher = CourseTeacher::where('id', $this->courseTeacherId)->select('group_id')->first();

        $contingent = $this->getStudents($courseTeacher->group_id);

        $journal = ModelsEJournal::where('course_teacher_id', $this->courseTeacherId)->orderBy('date_lesson', 'asc')->get();

        foreach ($journal as $key => $value) {
            $this->status[$value->id] = ($value->status) ? true : false;
        }

        $dates = ModelsEJournal::where('course_teacher_id', $this->courseTeacherId)
            ->select('date_lesson', 'time_lesson')
            ->distinct()
            ->orderBy('date_lesson', 'asc')
            ->get();

        return view('livewire.e-journal', [
            'contingent' => $contingent,
            'journal' => $journal,
            'dates' => $dates,
            'currentData' => $currentData,
            'currentTime' => $currentTime
        ]);
    }

    public function alertVisible(): void
    {
        $this->isAlertVisible = false;
    }

    public function store()
    {
        $courseTeacher = CourseTeacher::where('id', $this->courseTeacherId)->select('group_id')->first();
        $contingent = $this->getStudents($courseTeacher->group_id);

        foreach ($contingent as $key => $value) {
            ModelsEJournal::create([
                'student_id' => $value->user_id,
                'status' => false,
                'date_lesson' => Carbon::now()->format('Y-m-d'),
                'time_lesson' => $this->getLessonTime(Carbon::now()->format('H:i')),
                'course_teacher_id' => $this->courseTeacherId
            ]);
        }

        $this->isAlertVisible = true;

        session()->flash('message-success', 'Запись успешно добавлена!');
    }

    public function destroy($id)
    {
        $eJournal = ModelsEJournal::findOrFail($this->id);
        $eJournal->delete();

        $this->isAlertVisible = true;

        session()->flash('message-success', 'Запись успешно удалена!');
    }

    public function setStatus($id, $studentId)
    {
        $eJournal = ModelsEJournal::where('id', '=', $id)
            ->where('student_id', '=', $studentId)->update(['status' => $this->status[$id]]);
    }

    public function getStudents($groupId)
    {
        $contingent = new ContingentController();
        return $contingent->getContingent($groupId);
    }

    public function getLessonTime($currentTime)
    {
        $lessonTime = "";

        if (strtotime($currentTime) >= strtotime('08:20') && strtotime($currentTime) <= strtotime('09:50')) {
            $lessonTime = "08:20-09:50";
        } elseif (strtotime($currentTime) >= strtotime('10:00') && strtotime($currentTime) <= strtotime('11:30')) {
            $lessonTime = "10:00-11:30";
        } elseif (strtotime($currentTime) >= strtotime('11:40') && strtotime($currentTime) <= strtotime('13:10')) {
            $lessonTime = "11:40-13:10";
        } elseif (strtotime($currentTime) >= strtotime('13:30') && strtotime($currentTime) <= strtotime('15:00')) {
            $lessonTime = "13:30-15:00";
        } elseif (strtotime($currentTime) >= strtotime('15:10') && strtotime($currentTime) <= strtotime('16:40')) {
            $lessonTime = "15:10-16:40";
        } elseif (strtotime($currentTime) >= strtotime('17:00') && strtotime($currentTime) <= strtotime('18:30')) {
            $lessonTime = "17:00-18:30";
        } elseif (strtotime($currentTime) >= strtotime('18:40') && strtotime($currentTime) <= strtotime('20:10')) {
            $lessonTime = "18:40-20:10";
        } elseif (strtotime($currentTime) >= strtotime('20:20') && strtotime($currentTime) <= strtotime('21:50')) {
            $lessonTime = "20:20-21:50";
        } else {
            $lessonTime = "";
        }

        return $lessonTime;
    }
}
