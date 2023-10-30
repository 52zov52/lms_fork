<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class Achievement extends Component
{
    use WithFileUploads;

    public $achievement, $isAchievement = false;

    public bool $isAchievementModalVisible = false;

    public function render()
    {
        return view('livewire.achievement');
    }

    public function achievementModalVisible(): void
    {
        $this->isAchievementModalVisible = true;
    }

    public function addAchievement(): void
    {
        $this->achievement->store('achievement', 'public');
        session()->flash('message-success', 'Документ успешно загружен!');
    }
}
