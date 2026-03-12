<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class VacationsColorExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $year;

    public function __construct($year = null)
    {
        $this->year = $year ?? now()->year;
    }

    public function collection()
    {
        return Employee::whereNull('deleted_at')
            ->whereNotNull('vacation_start')
            ->whereNotNull('vacation_end')
            ->whereYear('vacation_start', $this->year)
            ->with('position')
            ->orderBy('last_name')
            ->get();
    }

    public function headings(): array
    {
        return [
            '№',
            'Фамилия',
            'Имя',
            'Отчество',
            'Должность',
            'Тип отпуска',
            'Начало',
            'Конец',
            'Дней',
            'Месяц',
        ];
    }

    public function map($emp): array
    {
        $startDate = Carbon::parse($emp->vacation_start);
        $endDate = Carbon::parse($emp->vacation_end);
        $days = $startDate->diffInDays($endDate) + 1;

        $typeLabels = [
            'annual' => 'Ежегодный',
            'sick' => 'Больничный',
            'unpaid' => 'За свой счёт',
            'study' => 'Учебный',
        ];

        return [
            $emp->id,
            $emp->last_name,
            $emp->first_name,
            $emp->middle_name ?? '',
            $emp->position->name ?? 'Без должности',
            $typeLabels[$emp->vacation_type] ?? 'Ежегодный',
            $startDate->format('d.m.Y'),
            $endDate->format('d.m.Y'),
            $days,
            $startDate->translatedFormat('F'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Заголовок
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Чередование строк
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            $color = $row % 2 === 0 ? 'F8FAFC' : 'FFFFFF';
            $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
            ]);
        }

        // Автофильтр
        $sheet->setAutoFilter('A1:J' . $highestRow);

        // Ширина колонок
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(12);
        $sheet->getColumnDimension('I')->setWidth(8);
        $sheet->getColumnDimension('J')->setWidth(12);

        return [];
    }

    public function title(): string
    {
        return '📋 Детали';
    }
}
