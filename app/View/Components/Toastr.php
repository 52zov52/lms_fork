<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Toastr extends Component
{
    public string $type;
    public string $message;
    
    public function __construct($type, $message)
    {
        $this->type = $type;
        $this->message = $message;
    }

    public function render(): View|Closure|string
    {
        return view('components.toastr');
    }
}
