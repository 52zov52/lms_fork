<?php

namespace App\Livewire\Components;

use App\Models\Achievement;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PortfolioRating extends Component
{
    public function render()
    {
        $score = Achievement::where('user_id', '=', Auth::user()->id)->sum('score');

        $portfolioRating = ($score / 100) * 100;

        return view('livewire.components.portfolio-rating', ['portfolioRating' => ($portfolioRating > 100) ? 100 : round($portfolioRating)]);
    }
}
