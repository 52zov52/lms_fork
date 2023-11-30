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

    public $id, $achievementCategory, $name, $description = '', $year, $file = null, $score = 0;

    public bool $isAchievementModalVisible = false, $isEdit = false;

    public function render()
    {
        $achievements = ModelsAchievement::where('user_id', '=', Auth::user()->id)->get();

        $educationalScore = ModelsAchievement::where('achievement_category', '=', 1)
            ->where('user_id', '=', Auth::user()->id)->sum('score');
        $scientificScore = ModelsAchievement::whereIn('achievement_category', [2, 3])
            ->where('user_id', '=', Auth::user()->id)->sum('score');
        $extracurricularScore = ModelsAchievement::whereIn('achievement_category', [4, 5])
            ->where('user_id', '=', Auth::user()->id)->sum('score');
        $otherScore = ModelsAchievement::where('achievement_category', '=', 6)
            ->where('user_id', '=', Auth::user()->id)->sum('score');

        return view('livewire.achievement', [
            'achievementCategories' => $this->achievementCategories,
            'achievements' => $achievements,
            'educationalScore' => $educationalScore,
            'scientificScore' => $scientificScore,
            'extracurricularScore' => $extracurricularScore,
            'otherScore' => $otherScore,
        ]);
    }

    public function achievementModalVisible($isEdit): void
    {
        if (!$isEdit) {
            $this->resetFields();
        }

        $this->isEdit = $isEdit;

        $this->isAchievementModalVisible = true;
    }

    public function store(): void
    {
        $this->validate([
            'achievementCategory' => 'required|min:1',
            'name' => 'required|string|max:255',
            'year' => 'required|numeric',
            'file' => 'file|mimes:pdf,jpg,bmp,png|max:3072', // 3MB Max
        ]);

        $filename = $this->file->store('achievement' . '/' . Carbon::now()->format('Y-m-d'), 'public');

        switch ($this->achievementCategory) {
            case 1:
                $this->score = 10;
                break;
            case 2:
                $this->score = 5;
                break;
            case 3:
                $this->score = 5;
                break;
            case 4:
                $this->score = 5;
                break;
            case 5:
                $this->score = 15;
                break;
            default:
                $this->score = 1;
                break;
        }

        ModelsAchievement::create([
            'achievement_category' => $this->achievementCategory,
            'name' => $this->name,
            'description' => $this->description,
            'year' => $this->year,
            'file_path' => $filename,
            'score' => $this->score,
            'user_id' => Auth::user()->id
        ]);

        $this->isAchievementModalVisible = false;

        session()->flash('message-success', 'Документ успешно загружен!');
    }

    public function edit($id, $isEdit): void
    {
        $this->id = $id;
        $achievement = ModelsAchievement::findOrFail($id);
        $this->achievementCategory = $achievement->achievement_category;
        $this->name = $achievement->name;
        $this->description = $achievement->description;
        $this->year = $achievement->year;

        $this->isEdit = $isEdit;

        $this->achievementModalVisible($isEdit);
    }

    public function update(): void
    {
        if (!is_null($this->file)) {
            $this->validate([
                'achievementCategory' => 'required|min:1',
                'name' => 'required|string|max:255',
                'year' => 'required|numeric',
                'file' => 'file|mimes:pdf,jpg,bmp,png|max:3072', // 3MB Max
            ]);
        } else {
            $this->validate([
                'achievementCategory' => 'required|min:1',
                'name' => 'required|string|max:255',
                'year' => 'required|numeric',
            ]);
        }

        switch ($this->achievementCategory) {
            case 1:
                $this->score = 10;
                break;
            case 2:
                $this->score = 5;
                break;
            case 3:
                $this->score = 5;
                break;
            case 4:
                $this->score = 5;
                break;
            case 5:
                $this->score = 15;
                break;
            default:
                $this->score = 1;
                break;
        }

        $achievement = ModelsAchievement::findOrFail($this->id);
        $achievement->achievement_category = $this->achievementCategory;
        $achievement->name = $this->name;
        $achievement->description = $this->description;
        $achievement->year = $this->year;

        if (!is_null($this->file)) {
            unlink(storage_path('app/public/' . $achievement->file_path));
            $filename = $this->file->store('achievement' . '/' . Carbon::now()->format('Y-m-d'), 'public');
            $achievement->file_path = $filename;
        }

        $achievement->score = $this->score;
        $achievement->update();

        $this->resetFields();

        $this->isAchievementModalVisible = false;

        session()->flash('message-success', 'Запись успешно обновлена!');
    }

    public function destroy($id): void
    {
        $achievement = ModelsAchievement::findOrFail($id);

        unlink(storage_path('app/public/' . $achievement->file_path));

        $achievement->delete();

        session()->flash('message-success', 'Запись успешно удалена!');
    }

    private function resetFields(): void
    {
        $this->achievementCategory = 0;
        $this->name = '';
        $this->description = '';
        $this->year = '';
        $this->file = null;
    }
}
