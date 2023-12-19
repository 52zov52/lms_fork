<?php

namespace App\Livewire;

use App\Models\Achievement as ModelsAchievement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Achievement extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $achievementCategories = [
        1 => 'Образование и профессиональное развитие',
        2 => 'Научно-исследовательская деятельность',
        3 => 'Учебно-методическая деятельность',
        4 => 'Общественная и творческая деятельность',
        5 => 'Профессиональные достижения и проекты',
        6 => 'Прочее'
    ];

    public $id, $achievementCategory = 0, $name, $description = '', $year, $file = null, $score = 0;

    public bool $isAchievementModalVisible = false, $isEdit = false, $isAlertVisible = false;

    public string $filename = '';
    public int $rand = 1;

    public function render()
    {
        $achievements = ModelsAchievement::where('user_id', '=', Auth::user()->id)->paginate(25);

        return view('livewire.achievement', [
            'achievementCategories' => $this->achievementCategories,
            'achievements' => $achievements
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

    public function alertVisible(): void
    {
        $this->isAlertVisible = false;
    }

    public function store(): void
    {
        $this->validate([
            'achievementCategory' => 'required|numeric|min:1',
            'name' => 'required|string|max:255',
            'year' => 'required|numeric',
            'file' => 'file|mimes:pdf,jpg,bmp,png|max:3072', // 3MB Max
        ]);

        $filename = $this->file->store('achievement' . '/' . Carbon::now()->format('Y-m-d'), 'public');

        switch ($this->achievementCategory) {
            case 1:
                $this->score = 2;
                break;
            case 2:
                $this->score = 2;
                break;
            case 3:
                $this->score = 1;
                break;
            case 4:
                $this->score = 2;
                break;
            case 5:
                $this->score = 2;
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
        $this->isAlertVisible = true;

        $this->resetFields();

        session()->flash('message-success', 'Документ успешно загружен!');
    }

    public function edit($id, $isEdit): void
    {
        $this->resetFields();

        $this->id = $id;
        $achievement = ModelsAchievement::findOrFail($id);
        $this->achievementCategory = $achievement->achievement_category;
        $this->name = $achievement->name;
        $this->description = $achievement->description;
        $this->year = $achievement->year;
        $this->file = $achievement->file_path;
        
        if(!is_null($achievement->file_path)) {
            $data = explode('/', $achievement->file_path);
            $this->filename = $data[2];
        }
            
        $this->isEdit = $isEdit;

        $this->achievementModalVisible($isEdit);
    }

    public function update(): void
    {
        if (!is_null($this->file)) {
            $this->validate([
                'achievementCategory' => 'required|numeric|min:1',
                'name' => 'required|string|max:255',
                'year' => 'required|numeric',
                'file' => 'file|mimes:pdf,jpg,bmp,png|max:3072', // 3MB Max
            ]);
        } else {
            $this->validate([
                'achievementCategory' => 'required|numeric|min:1',
                'name' => 'required|string|max:255',
                'year' => 'required|numeric',
            ]);
        }

        switch ($this->achievementCategory) {
            case 1:
                $this->score = 2;
                break;
            case 2:
                $this->score = 2;
                break;
            case 3:
                $this->score = 1;
                break;
            case 4:
                $this->score = 2;
                break;
            case 5:
                $this->score = 2;
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

        $this->isAchievementModalVisible = false;
        $this->isAlertVisible = true;

        $this->resetFields();

        session()->flash('message-success', 'Запись успешно обновлена!');
    }

    public function destroy($id): void
    {
        $achievement = ModelsAchievement::findOrFail($id);

        unlink(storage_path('app/public/' . $achievement->file_path));

        $achievement->delete();

        $this->isAlertVisible = true;

        session()->flash('message-success', 'Запись успешно удалена!');
    }

    private function resetFields(): void
    {
        $this->achievementCategory = 0;
        $this->name = '';
        $this->description = '';
        $this->year = '';
        $this->file = null;

        $this->rand++;

        $this->resetErrorBag();
        $this->resetValidation();
    }
}
