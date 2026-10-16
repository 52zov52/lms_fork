<?php

namespace App\Support;

/**
 * Приведение к единому виду значений, которые выводятся в отчете по студентам:
 * гражданство, документ об образовании, город, год выдачи.
 * Данные в базе не меняются - только то, что попадает в отчет.
 */
class StudentReportValues
{
    /** Страны: фрагмент названия (нижний регистр) => официальное название */
    private const COUNTRIES = [
        'азербайджан' => 'Азербайджанская Республика',
        'таджикистан' => 'Республика Таджикистан',
        'армени' => 'Республика Армения',
        'казахстан' => 'Республика Казахстан',
        'беларус' => 'Республика Беларусь',
        'белорус' => 'Республика Беларусь',
        'узбекистан' => 'Республика Узбекистан',
        'киргиз' => 'Кыргызская Республика',
        'кыргыз' => 'Кыргызская Республика',
        'молдов' => 'Республика Молдова',
        'туркмен' => 'Туркменистан',
        'росси' => 'Российская Федерация',
        'грузи' => 'Грузия',
        'украин' => 'Украина',
    ];

    /**
     * Документ об образовании: синонимы одного и того же документа (регулярное выражение => замена).
     * Применяются к названию в нижнем регистре.
     */
    private const DOCUMENT_RULES = [
        // опечатки
        ['/с отличие м\b/u', 'с отличием'],
        ['/образовани\s+/u', 'образовании '],
        // основное общее образование
        ['/\bоб (общем основном|основном) образовании/u', 'об основном общем образовании'],
        // среднее общее образование
        ['/\bо (полном среднем общем|полном общем|полном среднем|среднем \(полном\) общем|общем среднем|среднем) образовании/u', 'о среднем общем образовании'],
        ['/\bоб общем среднем образовании/u', 'о среднем общем образовании'],
        // среднее профессиональное образование
        ['/\bо среднем (специальном|специализированном) образовании/u', 'о среднем профессиональном образовании'],
        ['/\bо среднем профессиональном обучении/u', 'о среднем профессиональном образовании'],
    ];

    /** Слова, которые в названии документа пишутся заглавными */
    private const KEEP_UPPER = ['рф' => 'РФ'];

    /** Гражданство */
    public static function country(?string $value): string
    {
        $value = self::clean($value);

        if ($value === '') {
            return '';
        }

        $key = mb_strtolower($value);

        foreach (self::COUNTRIES as $fragment => $official) {
            if (str_contains($key, $fragment)) {
                return $official;
            }
        }

        return $value;
    }

    /** Документ об образовании: "аттестат", "Диплом ", "диплом бакалавра с отличием" и т.п. */
    public static function documentType(?string $value): string
    {
        $value = self::clean($value);

        if ($value === '') {
            return '';
        }

        $key = mb_strtolower($value);

        foreach (self::DOCUMENT_RULES as [$pattern, $replacement]) {
            $key = preg_replace($pattern, $replacement, $key);
        }

        $key = trim(preg_replace('/\s+/u', ' ', $key));

        foreach (self::KEEP_UPPER as $word => $upper) {
            $key = preg_replace('/\b' . $word . '\b/u', $upper, $key);
        }

        return self::upperFirst($key);
    }

    /** Город: без лишних пробелов, "Матвеев - Курган" -> "Матвеев-Курган", "ё" -> "е", с заглавной буквы */
    public static function city(?string $value): string
    {
        $value = self::clean($value);

        if ($value === '') {
            return '';
        }

        $value = preg_replace('/\s*-\s*/u', '-', $value);
        $value = str_replace(['ё', 'Ё'], ['е', 'Е'], $value);

        return self::upperFirst($value);
    }

    /** Год выдачи документа: заглушки вроде -1 или 0 не выводим */
    public static function year($value)
    {
        if ($value === null || $value === '' || !is_numeric($value)) {
            return '';
        }

        $year = (int)$value;

        return ($year < 1950 || $year > (int)date('Y') + 1) ? '' : $year;
    }

    private static function clean(?string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', (string)$value));
    }

    private static function upperFirst(string $value): string
    {
        return mb_strtoupper(mb_substr($value, 0, 1)) . mb_substr($value, 1);
    }
}
