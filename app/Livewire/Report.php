<?php

namespace App\Livewire;

use App\Http\Controllers\ScheduleController;
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
        {
            $scheduleController = new ScheduleController();
            $this->teachers = $scheduleController->getTeachers();
        }

        $this->isTeacherModalVisible = true;
    }

    public function groupModalVisible(): void
    {
        if(count($this->groups) == 0)
        {
            $scheduleController = new ScheduleController();
            $this->groups = $scheduleController->getGroups();
        }

        $this->isGroupModalVisible = true;
    }

    public function roomsModalVisible(): void
    {
        if(count($this->weeksRange) == 0)
        {
            $scheduleController = new ScheduleController();
            $this->weeksRange = $scheduleController->getWeeksRange();
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
    public function generateLoadRoomsReport()
    {
        return redirect()->route('generate.load.rooms.report', ['week' => $this->week]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function printCountPublicationByYearsAndStatuses()
    {
        return redirect()->route('print.count.publication.by.years.and.statuses');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function printCountUserPublicationsByStatusAndYearRange()
    {
        return redirect()->route('print.count.user.publications.by.status.and.year.range');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateContingentReport()
    {
        return redirect()->route('generate.contingent.report');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateContingentByAgeAndGenderReport()
    {
        return redirect()->route('generate.contingent.by.age.and.gender.report');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateContingentByAgeAndGenderWithoutTeachingFromReport()
    {
        return redirect()->route('generate.contingent.by.age.and.gender.without.teaching.from.report');
    }
}
