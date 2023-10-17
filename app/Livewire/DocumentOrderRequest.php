<?php

namespace App\Livewire;

use App\Models\DocumentOrderRequest as ModelsDocumentOrderRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DocumentOrderRequest extends Component
{
    public $documentType = 0;

    public function render()
    {
        $documentOrderRequest = ModelsDocumentOrderRequest::where('requester_id', '=', Auth::user()->id)->get();
        
        return view('livewire.document-order-request', [
            'documentOrderRequest' => $documentOrderRequest
            ]);
    }

    public function orderDocument(): void
    {
        if ($this->documentType != 0) {
            ModelsDocumentOrderRequest::create([
                'document_type' => $this->documentType,
                'status' => 1,
                'requester_id' => Auth::user()->id,
                'request_date' => Carbon::now()
            ]);

            session()->flash('message', 'Ок');
        } else {
            session()->flash('message', 'Ошибка');
        }
    }
}
