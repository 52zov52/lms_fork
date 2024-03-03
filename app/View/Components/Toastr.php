<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Toastr extends Component
{
    public string $message;
    public bool $show;

    public function __construct($message, $show = false)
    {
        $this->message = $message;
        $this->show = $show;
    }

    public function render(): View|Closure|string
    {
        return view('components.toastr');
    }
}
