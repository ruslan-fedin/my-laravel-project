<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Carbon\Carbon;

class VacationsSummaryExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $year;

    public function __construct($year = null)
    {
        $this->year = $year ?? now()->year;
    }

    public function array(): array
    {
        $employees = Employee::whereNull('deleted_at')
            ->whereNotNull('vacation_start')
            ->whereNotNull('vacation_end')
            ->whereYear('vacation_start', $this->year)
            ->get();

        $summary = [];

        // Общая статистика
        $total = $employees->count();
        $totalDays = $employees->sum(function($emp) {
            return Carbon::parse($emp->vacation_start)->diffInDays(Carbon::parse($emp->vacation_end)) + 1;
        });

        $byType = $employees->groupBy('vacation_type')->map(function($group) {
            return $group->count();
        });

        $summary[] = ['ОБЩАЯ СТАТИСТИКА', ''];
        $summary[] = ['Всего отпусков', $total];
        $summary[] = ['Всего дней', $totalDays];
        $summary[] = ['', ''];

        $summary[] = ['ПО ТИПАМ ОТПУСКОВ', ''];
        $typeLabels = [
            'annual' => 'Ежегодный',
            'sick' => 'Больничный',
            'unpaid' => 'За свой счёт',
            'study' => 'Учебный',
        ];

        foreach ($typeLabels as $key => $label) {
            $summary[] = [$label, $byType->get($key, 0)];
        }

        $summary[] = ['', ''];
        $summary[] = ['ПО МЕСЯЦАМ', ''];

        for ($month = 1; $month <= 12; $month++) {
            $count = $employees->filter(function($emp) use ($month) {
                return Carbon::parse($emp->vacation_start)->month === $month;
            })->count();
            $monthName = Carbon::create()->month($month)->translatedFormat('F');
            $summary[] = [$monthName, $count];
        }

        return $summary;
    }

    public function headings(): array
    {
        return ['Показатель', 'Значение'];
    }

    public function styles(Worksheet $sheet)
    {
        // Заголовок
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Ширина колонок
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(15);

        return [];
    }

    public function title(): string
    {
        return '📊 Статистика';
    }
}
