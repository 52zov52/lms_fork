<?php

namespace App\Livewire\Components;

use App\Models\DocumentOrderRequest;
use Livewire\Component;

class ApplicationCounter extends Component
{
    public function render()
    {
        $countRequest = DocumentOrderRequest::where('status', '=', 1)->count();

        return view('livewire.components.application-counter', ['countRequest' => $countRequest]);
    }
}
