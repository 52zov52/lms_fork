<?php

namespace App\Livewire;

use App\Http\Controllers\ScheduleController;
use App\Models\Schedule;
use Livewire\Component;

class Report extends Component
{
    public bool $isTeacherModalVisible = false, $isGroupModalVisible = false, $isRoomsModalVisible = false;
    public $teachers = [], $groups = [], $weeksRange = [];
    public string $fio = "", $group = "", $week = "";

    public function render()
    {
        return view('livewire.report');
    }

    public function teacherModalVisible(): void
    {
        if(count($this->teachers) == 0)
            $this->teachers = Schedule::select('teacher')->distinct()->orderBy('teacher', 'ASC')->get();
        
            $this->isTeacherModalVisible = true;
    }

    public function groupModalVisible(): void
    {
        if(count($this->groups) == 0)
            $this->groups = Schedule::select('group')->distinct()->orderBy('group', 'ASC')->get();
        
            $this->isGroupModalVisible = true;
    }

    public function roomsModalVisible(): void
    {
        if(count($this->weeksRange) == 0)
        {
            $weeks = Schedule::select('week', 'date')->distinct()->orderBy('week', 'ASC')->orderBy('date', 'ASC')->get()->toArray();
            $scheduleController = new ScheduleController();
            $this->weeksRange = $scheduleController->getWeeksRange($weeks);
        }
        
        $this->isRoomsModalVisible = true;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateTeacherReport()
    {
        return redirect()->route('generate.teacher.report', ['fio' => $this->fio]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateGroupReport()
    {
        return redirect()->route('generate.group.report', ['group' => $this->group]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateRoomsReport()
    {
        return redirect()->route('generate.rooms.report', ['week' => $this->week]);
    }
}
