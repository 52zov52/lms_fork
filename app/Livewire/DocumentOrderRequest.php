<?php

namespace App\Livewire;

use App\Models\DocumentOrderRequest as ModelsDocumentOrderRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DocumentOrderRequest extends Component
{
    use WithPagination;
    
    public int $documentType = 0;

    public bool $isAlertVisible = false, $isConfirmOrderDocumentModalVisible = false;

    public function render()
    {
        $documentOrderRequest = ModelsDocumentOrderRequest::where('requester_id', '=', Auth::user()->id)->paginate(25);
        
        return view('livewire.document-order-request', [
            'documentOrderRequest' => $documentOrderRequest
            ]);
    }

    public function alertVisible(): void
    {
        $this->isAlertVisible = false;
    }

    public function confirmOrderDocumentModalVisible(): void
    {
        $this->isConfirmOrderDocumentModalVisible = true;
    }

    public function store(): void
    {
        if ($this->documentType != 0) {
            ModelsDocumentOrderRequest::create([
                'document_type' => $this->documentType,
                'status' => 1,
                'requester_id' => Auth::user()->id,
                'request_date' => Carbon::now()
            ]);

            $this->isConfirmOrderDocumentModalVisible = false;
            $this->isAlertVisible = true;
            session()->flash('message-success', 'Заявка успешно создана!');
        } else {
            $this->isConfirmOrderDocumentModalVisible = false;
            $this->isAlertVisible = true;
            session()->flash('message-error', 'Ошибка в процессе создания заявки! Не выбран тип документа (справки).');
        }
    }
}
