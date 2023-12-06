<?php

namespace App\Livewire\Components;

use App\Models\DocumentOrderRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ApplicationCounter extends Component
{
    public function render()
    {
        if (Auth::user()->can('access-to-student-requests-for-certificates-full')) {
            $countRequest = DocumentOrderRequest::where('status', '=', 1)->count();
        } else if (Auth::user()->can('access-to-student-requests-for-certificates-spo')) {
            $countRequest = DocumentOrderRequest::join('users', 'document_order_requests.requester_id', '=', 'users.id')
                ->join('group_student', 'users.id', '=', 'group_student.user_id')
                ->join('groups', 'group_student.group_id', '=', 'groups.id')
                ->join('study_plans', 'groups.study_plan_id', '=', 'study_plans.id')
                ->join('specialities', 'study_plans.speciality_id', '=', 'specialities.id')
                ->where('specialities.level_education_id', '=', 2)
                ->where('document_order_requests.status', '=', 1)
                ->count();
        } else if (Auth::user()->can('access-to-student-requests-for-certificates-vo')) {
            $countRequest = DocumentOrderRequest::join('users', 'document_order_requests.requester_id', '=', 'users.id')
                ->join('group_student', 'users.id', '=', 'group_student.user_id')
                ->join('groups', 'group_student.group_id', '=', 'groups.id')
                ->join('study_plans', 'groups.study_plan_id', '=', 'study_plans.id')
                ->join('specialities', 'study_plans.speciality_id', '=', 'specialities.id')
                ->where('specialities.level_education_id', '<>', 2)
                ->where('document_order_requests.status', '=', 1)
                ->count();
        } else {
            $countRequest = 0;
        }

        return view('livewire.components.application-counter', ['countRequest' => $countRequest]);
    }
}
