<?php

namespace App\Livewire;

use App\Models\Achievement as ModelsAchievement;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class Achievement extends Component
{
    use WithFileUploads;

    public $achievementCategories = [
        1 => 'Образование и профессиональное развитие',
        2 => 'Научно-исследовательская деятельность',
        3 => 'Учебно-методическая деятельность',
        4 => 'Общественная и творческая деятельность',
        5 => 'Профессиональные достижения и проекты',
        6 => 'Прочее'
    ];

    public $achievementCategory, $name, $description, $year, $file;

    public bool $isAchievementModalVisible = false;

    public function render()
    {
        return view('livewire.achievement', ['achievementCategories' => $this->achievementCategories]);
    }

    public function achievementModalVisible(): void
    {
        $this->isAchievementModalVisible = true;
    }

    public function addAchievement(): void
    {
        $this->validate([
            'achievementCategory' => 'required|min:1',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1500',
            'year' => 'required|string',
            'file' => 'mimes:pdf|max:3072', // 3MB Max
        ]);

        $this->file->store('achievement', 'public');

        ModelsAchievement::create([
            'achievement_category' => $this->achievementCategory,
            'name' => $this->name,
            'description' => $this->description,
            'year' => $this->year,
            'file_path' => '$this->',
            'user_id' => Auth::user()->id
        ]);

        session()->flash('message-success', 'Документ успешно загружен!');
    }
}
