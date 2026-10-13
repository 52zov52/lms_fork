<?php

namespace Database\Seeders;

use App\Models\OpopLeader;
use Illuminate\Database\Seeder;

class OpopLeadersSeeder extends Seeder
{
    public function run(): void
    {
        $leaders = [
            'Артамонова Анна Геннадьевна',
            'Дружинина Валерия Павловна',
            'Кабаненко Михаил Николаевич',
            'Коренякина Наталья Николаевна',
            'Бабаева Марина Викторовна',
            'Колпащикова Оксана Андреевна',
            'Жолобова Елена Александровна',
            'Мурадова Сафура Шиховна',
            'Леонова Ольга Александровна',
            'Фоменко Андрей Иванович',
            'Григорьева Наталья Станиславовна',
            'Мартынов Борис Викторович',
        ];

        foreach ($leaders as $fio) {
            OpopLeader::updateOrCreate(
                ['fio' => $fio],
                ['is_active' => true]
            );
        }
    }
}