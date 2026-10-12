<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\StudentDocumentOrderIssuance;
use App\Models\StudentDocumentOrderRequest;
use App\Models\UserData;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;

class StudentDocumentOrderReportController extends Controller
{
    private const TYPE_STUDY = 1;              // Справка о статусе студента
    private const TYPE_TRANSPORT_CARD = 2;     // Справка для транспортных карт (только колледж)
    private const TYPE_INVITATION = 3;         // Справка-вызов
    private const TYPE_CHARACTERISTIC = 4;     // Характеристика

    private const LEVEL_SPO = 'Среднее профессиональное';
    private const LEVEL_BACHELOR = 'Бакалавриат';
    private const LEVEL_MASTER = 'Магистратура';

    private const MONTHS_GENITIVE = [
        1 => 'января',   2 => 'февраля',  3 => 'марта',
        4 => 'апреля',   5 => 'мая',      6 => 'июня',
        7 => 'июля',     8 => 'августа',  9 => 'сентября',
        10 => 'октября', 11 => 'ноября',  12 => 'декабря',
    ];

    private function formatLeaveOrders(?string $leaveOrders): string
    {
        $lines = array_filter(array_map('trim', preg_split('/\R/u', (string) $leaveOrders)));
 
        return implode(
            '</w:t><w:br/><w:t xml:space="preserve">',
            array_map(fn($line) => htmlspecialchars($line, ENT_XML1 | ENT_QUOTES, 'UTF-8'), $lines)
        );
    }

    public function downloadStudentDocument($studentDocumentOrderRequestId)
    {
        $request = StudentDocumentOrderRequest::findOrFail($studentDocumentOrderRequestId);
        $issuance = StudentDocumentOrderIssuance::where('student_document_order_request_id', '=', $studentDocumentOrderRequestId)
            ->firstOrFail();

        $type = (int) $request->document_type;

        $path = $this->resolveTemplatePath($type, $issuance->level_education_name);
        $template = new TemplateProcessor($path);

        // Общие поля для всех типов документов
        // Дата выдачи справки = дата подтверждения
        $template->setValue('date', $this->formatDateWithMonthName($issuance->confirmed_at ?? $issuance->updated_at));
        $template->setValue('number', $issuance->document_number . '-эл.');
        $template->setValue('course', $issuance->course);
        $template->setValue('teaching_form', $this->transformTeachingForm($issuance->teaching_form_name));
        $template->setValue('signer_position', $issuance->signer_position);
        $template->setValue('signer_fio', $issuance->signer_fio);

        // Дательный падеж ФИО (справки 1, 2, 3)
        if (in_array($type, [self::TYPE_STUDY, self::TYPE_TRANSPORT_CARD, self::TYPE_INVITATION], true)) {
            $template->setValue('last_name_dative', $issuance->last_name_dative);
            $template->setValue('first_name_dative', $issuance->first_name_dative);
            $template->setValue('middle_name_dative', $issuance->middle_name_dative);
        }

        // Поля, общие для справок об обучении и характеристики (1, 2, 4)
        if ($type !== self::TYPE_INVITATION) {
            $confirmiter = UserData::select('last_name', 'first_name', 'middle_name')
                ->where('user_id', '=', $issuance->confirmiter_id)
                ->first();

            $template->setValue('birthday', $this->formatDateWithMonthName($issuance->birthday));
            $template->setValue('edu_level', $this->buildEduLevel($issuance->level_education_name, $type));
            $template->setValue('sex', $issuance->sex ? 'она' : 'он');
            $template->setValue('student_word', $this->studentWord($issuance->sex));
            $template->setValue('vo_level', $this->voLevelWord($issuance->level_education_name));
            $template->setValue('sp', $issuance->specialty_code . ' - ' . $issuance->speciality_name);
            $template->setValue('orders', $issuance->orders);
            $template->setValue('training_period', $issuance->training_period);
            $template->setValue('confirmiter', $this->formatConfirmiter($confirmiter));
        }

        // Только справки об обучении (1, 2)
        if (in_array($type, [self::TYPE_STUDY, self::TYPE_TRANSPORT_CARD], true)) {
            $template->setValue('is_budget', $issuance->is_budget
                ? ' за счет средств бюджетных ассигнований федерального бюджета '
                : '');
 
            $template->setValue('academic_leave', $issuance->had_academic_leave ? 'предоставлялся' : 'не предоставлялся');
 
            $template->setValue('scholarship_end', $issuance->is_budget
                ? '.'
                : ', стипендия не выплачивается, иные меры социальной поддержки обучающихся не оказываются.');
 
            $leaveOrders = $this->formatLeaveOrders($issuance->leave_orders ?? null);

            $this->setOptionalBlock($template, 'leave_orders_block', $leaveOrders);
            $template->setValue('leave_oders', $leaveOrders);
        }

        // ФИО в именительном падеже: справка-вызов (3) и характеристика (4)
        if ($type === self::TYPE_INVITATION || $type === self::TYPE_CHARACTERISTIC) {
            $template->setValue('last_name', $issuance->last_name);
            $template->setValue('first_name', $issuance->first_name);
            $template->setValue('middle_name', $issuance->middle_name);
        }

        // Только характеристика (4)
        if ($type === self::TYPE_CHARACTERISTIC) {
            $template->setValue('last_name_genitive', $issuance->last_name_genitive);
            $template->setValue('first_name_genitive', $issuance->first_name_genitive);
            $template->setValue('middle_name_genitive', $issuance->middle_name_genitive);
        }

        // Только справка-вызов (3)
        if ($type === self::TYPE_INVITATION) {
            $template->setValue('specialty_code', $issuance->specialty_code);
            $template->setValue('speciality_name', $issuance->speciality_name);
            $template->setValue('check_in_date', Carbon::parse($issuance->check_in_date)->format('d.m.Y'));
            $template->setValue('check_out_date', Carbon::parse($issuance->check_out_date)->format('d.m.Y'));
            $template->setValue('check_in_duration', $issuance->check_in_duration);
            $template->setValue('employment_place', $issuance->employment_place);
        }

        $filename = time() . '.docx';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $template->saveAs($tempFile);

        return ['tempFile' => $tempFile, 'filename' => $filename];
    }

    /**
     * Выбор шаблона .docx по типу документа и уровню образования.
     *
     * @throws DomainException если для сочетания типа и уровня шаблона нет
     */
    private function resolveTemplatePath(int $type, ?string $levelName): string
    {
        $isSpo = $levelName === self::LEVEL_SPO;

        $relativePath = match (true) {
            $type === self::TYPE_STUDY && $isSpo => 'template/certificates/tmp_about_study_spo.docx',
            $type === self::TYPE_STUDY => 'template/certificates/tmp_about_study_vo.docx',
            $type === self::TYPE_TRANSPORT_CARD && $isSpo => 'template/certificates/tmp_about_study_transp_spo.docx',
            $type === self::TYPE_TRANSPORT_CARD => throw new DomainException('Справка для транспортных карт выдаётся только студентам колледжа.'),
            $type === self::TYPE_INVITATION => 'template/spravka.docx',
            default => 'template/certificates/tmp_characteristic.docx',
        };

        return Storage::disk('public')->path($relativePath);
    }

    private function buildEduLevel(?string $levelName, int $type): string
    {
        switch ($levelName) {
            case self::LEVEL_SPO:
                $forTransportCard = ($type === self::TYPE_TRANSPORT_CARD)
                    ? 'интегрированной с общеобразовательной программой среднего общего образования'
                    : '';

                return trim("среднего профессионального образования $forTransportCard")
                    . ' - программе подготовки специалистов среднего звена специальности';
            case self::LEVEL_BACHELOR:
                return 'бакалавриата направления подготовки';
            case self::LEVEL_MASTER:
                return 'магистратуры направления подготовки';
            default:
                return '';
        }
    }

    private function formatConfirmiter(?UserData $confirmiter): string
    {
        if (is_null($confirmiter)) {
            return '';
        }

        return $confirmiter->last_name . ' '
            . mb_substr($confirmiter->first_name, 0, 1) . '.'
            . mb_substr($confirmiter->middle_name, 0, 1) . '.';
    }

    // 1 -> "обучающейся", 0 -> "обучающимся"
    private function studentWord($sex): string
    {
        return $sex ? 'обучающейся' : 'обучающимся';
    }

    // "Бакалавриат" -> "бакалавриат", "Магистратура" -> "магистратура", для СПО пусто
    private function voLevelWord(?string $levelName): string
    {
        return match ($levelName) {
            self::LEVEL_BACHELOR => 'бакалавриат',
            self::LEVEL_MASTER => 'магистратура',
            default => '',
        };
    }

    // "2026-03-05" -> "5 марта 2026"
    private function formatDateWithMonthName($date): string
    {
        $d = Carbon::parse($date);

        return $d->day . ' ' . self::MONTHS_GENITIVE[$d->month] . ' ' . $d->year;
    }

    private function setOptionalBlock(TemplateProcessor $template, string $block, ?string $value): void
    {
        if (is_null($value) || trim($value) === '') {
            // deleteBlock() в PhpWord ломает XML, поэтому удаляем блок через cloneBlock с 0 копий
            $template->cloneBlock($block, 0, true);
            return;
        }

        $template->cloneBlock($block, 1, true);
    }

    private function transformTeachingForm($teachingForm): string
    {
        switch ($teachingForm) {
            case 'Очная':
                return 'очной';
            case 'Заочная':
                return 'заочной';
            case 'Заочная форма (ускоренное обучение)':
                return 'заочной (ускоренное)';
            case 'Очно-заочная':
                return 'очно-заочной';
            case 'Очная (ускоренное обучение)':
                return 'очной (ускоренное)';
            default:
                return '';
        }
    }
}