<?php

namespace App\Livewire;

use App\Http\Controllers\ContingentController;
use App\Models\DocumentOrderRequest;
use App\Models\Group;
use Carbon\Carbon;
use Livewire\Component;

class QrCodeDocumentOrderRequests extends Component
{
    public $selectedDocumentTypeId = 0;
    public $selectedGroupId = 0;
    public $selectedUserId = 0;

    public function render()
    {
        $contingents = new ContingentController();
        $groupId = $contingents->groupId;

        $groups = Group::select('id', 'group_name')->whereIn('id', $groupId)->orderBy('group_name', 'asc')->get();
        $students = $contingents->getContingent($this->selectedGroupId);

        return view('livewire.qr-code-document-order-requests', [
            'groups' => $groups,
            'students' => $students
        ]);
    }

    public function setDocumentTypeId()
    {
        $this->selectedDocumentTypeId = $this->selectedDocumentTypeId;
    }

    public function setGroupId()
    {
        $this->selectedGroupId = $this->selectedGroupId;
    }

    public function setUserId()
    {
        $this->selectedUserId = $this->selectedUserId;
    }

    public function orderDocument(): void
    {
        if ($this->selectedDocumentTypeId != 0) {
            DocumentOrderRequest::create([
                'document_type' => $this->selectedDocumentTypeId,
                'status' => 1,
                'requester_id' => $this->selectedUserId,
                'request_date' => Carbon::now()
            ]);

            session()->flash('message-success', 'Заявка успешно создана!');
        } else {
            session()->flash('message-error', 'Ошибка в процессе создания заявки!');
        }
    }
}
