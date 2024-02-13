<?php

namespace App\Livewire;

use App\Http\Controllers\ContingentController;
use Carbon\Carbon;
use Livewire\Component;

class EJournal extends Component
{
    public function render()
    {
        $currentData = Carbon::now()->format('d.m.Y');
        $currentTime = $this->getLessonTime(Carbon::now()->format('H:i'));

        $contingent = $this->getStudents(2281);

        return view('livewire.e-journal', [
            'contingent' => $contingent,
            'currentData' => $currentData,
            'currentTime' => $currentTime
        ]);
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
        }elseif(strtotime($currentTime) >= strtotime('10:00') && strtotime($currentTime) <= strtotime('11:30')) {
            $lessonTime = "10:00-11:30";
        }elseif(strtotime($currentTime) >= strtotime('13:30') && strtotime($currentTime) <= strtotime('15:00')) {
            $lessonTime = "13:30-15:00";
        }elseif(strtotime($currentTime) >= strtotime('15:10') && strtotime($currentTime) <= strtotime('16:40')) {
            $lessonTime = "15:10-16:40";
        }elseif(strtotime($currentTime) >= strtotime('17:00') && strtotime($currentTime) <= strtotime('18:30')) {
            $lessonTime = "17:00-18:30";
        }elseif(strtotime($currentTime) >= strtotime('18:40') && strtotime($currentTime) <= strtotime('20:10')) {
            $lessonTime = "18:40-20:10";
        }elseif(strtotime($currentTime) >= strtotime('20:20') && strtotime($currentTime) <= strtotime('21:50')) {
            $lessonTime = "20:20-21:50";
        }else {
            $lessonTime = "";
        }

        return $lessonTime;
    }
}
