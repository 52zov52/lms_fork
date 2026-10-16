<?php

namespace App\Livewire;

use App\Http\Controllers\Report\ContingentReportController;
use App\Http\Controllers\Report\StudentsEducationReportController;
use App\Http\Controllers\Reports\SocialPassportController;
use App\Http\Controllers\ScheduleController;
use App\Models\EJournal;
use Illuminate\Support\Facades\Response;
use Livewire\Component;

class Report extends Component
{
    public bool $isTeacherModalVisible = false,
        $isGroupModalVisible = false,
        $isRoomsModalVisible = false,
        $isEJournalModalVisible = false,
        $isSocialPassportModalVisible = false,
        $isStudentsEducationModalVisible = false;

    public $teachers = [], $groups = [], $weeksRange = [];
    public string $fio = "", $group = "", $week = "";

    public $selectedAcademicYear = 0;
    public $selectedHalfYear = 0;
    public $selectedDiscipline = 0;
    public $selectedGroup = 0;

    public $listOfAcademicYear = [];
    public $listOfHalfYear = array(1, 2);
    public $listOfDiscipline = [];
    public $listOfGroups = [];

    public $childCategories = '';
    public $hasPushkinCard = 2;

    // Отчет по студентам: выбор колонок и формата
    public array $studentsEducationColumns = [];
    public string $studentsEducationFormat = 'xlsx';   // xlsx | docx | pdf

    public function render()
    {
        if (count($this->listOfAcademicYear) == 0) {
            $this->listOfAcademicYear = EJournal::select('academic_year')->distinct()->get();
        }

        if (count($this->listOfGroups) == 0) {
            $this->listOfGroups = EJournal::join('groups', 'e_journals.group_id', '=', 'groups.id')
                ->where('academic_year', '=', $this->selectedAcademicYear)
                ->where('half_year', '=', $this->selectedHalfYear)
                ->select(
                    'e_journals.group_id',
                    'groups.group_name'
                )
                ->distinct()
                ->get();
        }

        if (count($this->listOfDiscipline) == 0 && $this->selectedGroup != 0) {
            $this->listOfDiscipline = EJournal::join('work_study_plan_courses_in_semesters', 'e_journals.work_study_plan_courses_in_semester_id', '=', 'work_study_plan_courses_in_semesters.id')
                ->join('work_study_plan_courses', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
                ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
                ->where('e_journals.academic_year', '=', $this->selectedAcademicYear)
                ->where('e_journals.half_year', '=', $this->selectedHalfYear)
                ->where('e_journals.group_id', '=', $this->selectedGroup)
                ->select(
                    'e_journals.work_study_plan_courses_in_semester_id',
                    'disciplines.name'
                )
                ->distinct()
                ->get();
        }

        return view('livewire.report');
    }

    public function teacherModalVisible(): void
    {
        if (count($this->teachers) == 0) {
            $scheduleController = new ScheduleController();
            $this->teachers = $scheduleController->getTeachers();
        }

        $this->isTeacherModalVisible = true;
    }

    public function groupModalVisible(): void
    {
        if (count($this->groups) == 0) {
            $scheduleController = new ScheduleController();
            $this->groups = $scheduleController->getGroups();
        }

        $this->isGroupModalVisible = true;
    }

    public function roomsModalVisible(): void
    {
        if (count($this->weeksRange) == 0) {
            $scheduleController = new ScheduleController();
            $this->weeksRange = $scheduleController->getWeeksRange();
        }

        $this->isRoomsModalVisible = true;
    }

    public function showEJournalModal()
    {
        $this->isEJournalModalVisible = true;
    }

    public function showSocialPassportModal()
    {
        $this->isSocialPassportModalVisible = true;
    }

    public function setAcademicYear()
    {
        $this->selectedAcademicYear = $this->selectedAcademicYear;
        $this->selectedHalfYear = 0;
        $this->selectedGroup = 0;
        $this->selectedDiscipline = 0;
    }

    public function setHalfYear()
    {
        $this->selectedHalfYear = $this->selectedHalfYear;
        $this->selectedGroup = 0;
        $this->selectedDiscipline = 0;
    }

    public function setGroup()
    {
        $this->selectedGroup = $this->selectedGroup;
        $this->selectedDiscipline = 0;
    }

    public function setDiscipline()
    {
        $this->selectedDiscipline = $this->selectedDiscipline;
    }

    public function printApplicantAverageScore()
    {
        return redirect()->route('print.applicant.average.score');
    }

    public function printAdmissionCampaignToDay()
    {
        return redirect()->route('print.admission.campaign.to.day.report');
    }

    public function printAdmissionCampaignFull()
    {
        return redirect()->route('print.admission.campaign.full.report', ['id' => 0]);
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

    public function downloadListOfForeignStudents()
    {
        $contingentReportController = new ContingentReportController();
        $data = $contingentReportController->downloadListOfForeignStudents();

        return Response::download($data['tempFile'], $data['filename'])->deleteFileAfterSend(true);
    }

    public function downloadEJournalReport()
    {
        if ($this->selectedAcademicYear == 0 || $this->selectedHalfYear == 0 || $this->selectedGroup == 0 || $this->selectedDiscipline == 0) {
            $this->dispatch('show-toast', message: 'Не выбрано одно или несколько обязательных полей!', type: 'error');
        } else {
            $contingentReportController = new ContingentReportController();
            $data = $contingentReportController->downloadEJournalReport($this->selectedAcademicYear, $this->selectedHalfYear, $this->selectedGroup, $this->selectedDiscipline);

            return Response::download($data['tempFile'], $data['filename'])->deleteFileAfterSend(true);
        }
    }

    public function downloadSocialPassportReport()
    {
        $socialPassportController = new SocialPassportController();
        $data = $socialPassportController->downloadSocialPassportReport(array($this->childCategories), $this->hasPushkinCard);

        return Response::download($data['tempFile'], $data['filename'])->deleteFileAfterSend();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateSptReport()
    {
        return redirect()->route('generate.spt.report');
    }

    public function showStudentsEducationModal(): void
    {
        // Каждый раз открываем окно с основными колонками (ничего не запоминается)
        $this->resetStudentsEducationColumns();
        $this->studentsEducationFormat = 'xlsx';
        $this->isStudentsEducationModalVisible = true;
    }

    public function resetStudentsEducationColumns(): void
    {
        $this->studentsEducationColumns = StudentsEducationReportController::defaultColumns();
    }

    public function selectAllStudentsEducationColumns(): void
    {
        $this->studentsEducationColumns = array_keys(StudentsEducationReportController::columnOptions());
    }

    public function downloadStudentsEducationReport()
    {
        if (empty($this->studentsEducationColumns)) {
            $this->dispatch('show-toast', message: 'Выберите хотя бы одну колонку!', type: 'error');

            return;
        }

        $format = in_array($this->studentsEducationFormat, StudentsEducationReportController::FORMATS, true)
            ? $this->studentsEducationFormat
            : 'xlsx';

        try {
            $data = (new StudentsEducationReportController())->download($this->studentsEducationColumns, $format);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('show-toast', message: 'Не удалось сформировать отчет: ' . $e->getMessage(), type: 'error');

            return;
        }

        return Response::download($data['tempFile'], $data['filename'])->deleteFileAfterSend(true);
    }
}