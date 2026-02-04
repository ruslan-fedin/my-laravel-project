<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TimesheetExport implements FromView, WithColumnWidths, WithStyles
{
    protected $timesheet;
    protected $employees;
    protected $dates;

    public function __construct($timesheet, $employees, $dates)
    {
        $this->timesheet = $timesheet;
        $this->employees = $employees;
        $this->dates = $dates;
    }

    public function view(): View
    {
        $items = \App\Models\TimesheetItem::where('timesheet_id', $this->timesheet->id)->get()->groupBy('employee_id');
        $statuses = \App\Models\Status::all();

        return view('exports.timesheet_excel', [
            'timesheet' => $this->timesheet,
            'employees' => $this->employees,
            'dates' => $this->dates,
            'items' => $items,
            'statuses' => $statuses
        ]);
    }

    public function columnWidths(): array
    {
        // A - №, B - ФИО, C - Должность
        $widths = [
            'A' => 6,
            'B' => 45, // Широкая для полного ФИО
            'C' => 25, // Отдельная для должности
        ];

        // Динамические колонки для дат (начиная с D)
        $columnLetter = 'D';
        foreach ($this->dates as $date) {
            $widths[$columnLetter] = 5;
            $columnLetter++;
        }

        // Последняя колонка - Примечание (она идет сразу после дат)
        $widths[$columnLetter] = 40; // Сделали еще больше

        return $widths;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->employees) + 4;
        $lastColLetter = $sheet->getHighestColumn();

        // Устанавливаем высоту строк
        for ($i = 4; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(35);
        }

        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            4 => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']]
            ],
            'A4:' . $lastColLetter . $lastRow => [
                'alignment' => ['vertical' => 'center', 'wrapText' => true],
                'borders' => [
                    'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
                ],
            ],
        ];
    }
}
