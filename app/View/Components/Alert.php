<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Alert extends Component
{
    public string $type;
    public string $header;
    public string $message;

    public bool $static;

    public function __construct($type, $header, $message, $static = false)
    {
        $this->type = $type;
        $this->header = $header;
        $this->message = $message;
        
        $this->static = $static;
    }

    public function render(): View|Closure|string
    {
        return view('components.alert');
    }
}
