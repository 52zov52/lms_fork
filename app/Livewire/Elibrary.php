<?php

namespace App\Livewire;

use App\Models\Elibrary as ModelsElibrary;
use Livewire\Component;

class Elibrary extends Component
{
    public function render()
    {
        return view('livewire.elibrary', ['elibraries' => ModelsElibrary::get()]);
    }
}
