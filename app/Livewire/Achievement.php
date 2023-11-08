<?php

namespace App\Livewire;

use App\Models\Achievement as ModelsAchievement;
use Carbon\Carbon;
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

    public $achievementCategory, $name, $description = '', $year, $file;

    public bool $isAchievementModalVisible = false;

    public function render()
    {
        $achievements = ModelsAchievement::where('user_id', '=', Auth::user()->id)->get();

        return view('livewire.achievement', [
            'achievementCategories' => $this->achievementCategories,
            'achievements' => $achievements
        ]);
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
            'year' => 'required|string',
            'file' => 'file|mimes:pdf,jpg,bmp,png|max:3072', // 3MB Max
        ]);

        $filename = $this->file->store('achievement' . '/' . Carbon::now()->format('Y-m-d'), 'public');

        ModelsAchievement::create([
            'achievement_category' => $this->achievementCategory,
            'name' => $this->name,
            'description' => $this->description,
            'year' => $this->year,
            'file_path' => $filename,
            'user_id' => Auth::user()->id
        ]);

        $this->isAchievementModalVisible = false;

        session()->flash('message-success', 'Документ успешно загружен!');
    }
}
