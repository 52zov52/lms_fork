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

    public function render()
    {
        $documentOrderRequest = ModelsDocumentOrderRequest::where('requester_id', '=', Auth::user()->id)->paginate(25);
        
        return view('livewire.document-order-request', [
            'documentOrderRequest' => $documentOrderRequest
            ]);
    }

    public function callMethod($methodName)
    {
        $this->{$methodName}();
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

            session()->flash('type', 'success');
            session()->flash('message', 'Заявка успешно создана!');
        } else {
            session()->flash('type', 'error');
            session()->flash('message', 'Ошибка! Не выбран тип документа (справки).');
        }
    }
}
