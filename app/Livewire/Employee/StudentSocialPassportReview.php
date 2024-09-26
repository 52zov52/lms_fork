<?php

namespace App\Livewire\Employee;

use App\Http\Controllers\ContingentController;
use App\Http\Controllers\Employee\StudentSocialPassportReviewController;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\SocialPassport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StudentSocialPassportReview extends Component
{
    public int $groupId = 0;
    public $contingent = null;

    public function render()
    {
        $contingents = new ContingentController();
        $groups = Group::select('id', 'group_name')->whereIn('id', $contingents->getGroupIds())->orderBy('group_name', 'asc')->get();

        return view('livewire.employee.student-social-passport-review', [
            'groups' => $groups,
            'contingent' => $this->contingent
        ]);
    }

    public function callMethod($methodName, $param = null): void
    {
        $this->{$methodName}($param);
    }

    public function getContingent(): void
    {
        $this->contingent = GroupStudent::select(
            'group_student.user_id',
            'group_student.id',
            'users_data.last_name',
            'users_data.first_name',
            'users_data.middle_name',
            DB::raw("(SELECT ticket_number FROM student_ticket_numbers WHERE user_id = group_student.user_id ORDER BY id DESC LIMIT 1) as ticket_number"),
            'social_passports.questionnaire_status'
        )
            ->join('users_data', 'group_student.user_id', '=', 'users_data.user_id')
            ->leftJoin('social_passports', 'group_student.user_id', '=', 'social_passports.user_id')
            ->where('group_student.group_id', '=', $this->groupId)
            ->orderBy('users_data.last_name', 'asc')
            ->get();
    }

    public function download($userId) 
    {
        return redirect()->route('download.student.social.passport', ['userId' => $userId]);
    }

    public function confirm($userId)
    {
        $socialPassport = SocialPassport::where('user_id', '=', $userId)->first();

        if (!is_null($socialPassport)) {
            $socialPassport->questionnaire_status = 2;
            $socialPassport->confirmiter_id = Auth::user()->id;
            $socialPassport->confirmiter_signature = StudentSocialPassportReviewController::getSignature(Auth::user()->id);
            $socialPassport->save();

            $this->getContingent();

            session()->flash('type', 'success');
            session()->flash('message', 'Запись успешно обновлена!');
        } else {
            session()->flash('type', 'error');
            session()->flash('message', 'Ошибка обновления записи!');
        }
    }

    public function backToEdit($userId)
    {
        $socialPassport = SocialPassport::where('user_id', '=', $userId)->first();

        if (!is_null($socialPassport)) {
            $socialPassport->questionnaire_status = 0;
            $socialPassport->confirmiter_id = null;
            $socialPassport->confirmiter_signature = null;
            $socialPassport->user_signature = null;
            $socialPassport->save();

            $this->getContingent();

            session()->flash('type', 'success');
            session()->flash('message', 'Запись успешно обновлена!');
        } else {
            session()->flash('type', 'error');
            session()->flash('message', 'Ошибка обновления записи!');
        }
    }
}
