<?php

namespace App\Livewire\Employee;

use App\Http\Controllers\Employee\ContingentController;
use App\Models\Group;
use Livewire\Component;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;

class Contingent extends Component
{
    public int $groupId = 0;
    public $groups = [];
    public $contingent = [];

    public function render()
    {
        $contingentController = new ContingentController();

        $this->groups = Group::select('id', 'group_name')
            ->whereIn('id', $contingentController->getGroupIds())
            ->orderBy('group_name', 'asc')
            ->get();

        if ($this->groupId != 0) {
            $this->contingent = $contingentController->getContingent($this->groupId);
        }

        return view('livewire.employee.contingent');
    }

    public function callMethod($methodName, $param = null): void
    {
        $this->{$methodName}($param);
    }

    public function getContingent(): void
    {
        $contingentController = new ContingentController();
        $this->contingent = $contingentController->getContingent($this->groupId);
    }

    public function downloadListOfStudents()
    {
        if ($this->groupId != 0) {
            $contingentController = new ContingentController();
            $listOfStudents = $contingentController->getContingent($this->groupId);

            $filename = time() . '.xlsx';

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $offset = 1;
            $increment = 1;

            $sheet->setCellValue('A' . $offset, '№ пп');
            $sheet->setCellValue('B' . $offset, 'ФИО');
            $sheet->setCellValue('C' . $offset, '№ зачетки');

            $sheet->getStyle('A' . $offset . ':C' . $offset)->getFont()->setBold(true);

            $offset++;

            for ($i = 0; $i < count($listOfStudents); $i++) {
                $sheet->setCellValue('A' . $offset, $increment);
                $sheet->setCellValue('B' . $offset, $listOfStudents[$i]->last_name . ' ' . $listOfStudents[$i]->first_name . ' ' . $listOfStudents[$i]->middle_name);
                $sheet->setCellValue('C' . $offset, $listOfStudents[$i]->ticket_number);

                $offset++;
                $increment++;
            }

            //Выравнивание текста в ячейке
            $sheet->getStyle('A1:A' . ($offset - 1))
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            //Выравнивание текста в ячейке
            $sheet->getStyle('C1:C' . ($offset - 1))
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            //Стиль для рамки
            $sheet->getStyle('A1:C' . ($offset - 1))
                ->getBorders()
                ->getAllBorders()
                ->applyFromArray(array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '808080')
                ));

            //Устанавливаем ширину полей
            $sheet->getColumnDimension('A')->setWidth(10);
            $sheet->getColumnDimension('B')->setWidth(35);
            $sheet->getColumnDimension('C')->setWidth(15);

            $writer = new Xlsx($spreadsheet);

            $tempFile = tempnam(sys_get_temp_dir(), $filename);
            $writer->save($tempFile);

            return Response::download($tempFile, $filename)->deleteFileAfterSend(true);
        } else {
            session()->flash('type', 'error');
            session()->flash('message', 'Невозможно сформировать отчет, т.к. не выбрана группа из списка!');
        }
    }
}
