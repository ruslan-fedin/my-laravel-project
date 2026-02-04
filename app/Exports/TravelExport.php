<?php

namespace App\Exports;

use App\Models\TravelItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TravelExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $start, $end, $statusId;

    public function __construct($start, $end, $statusId = null)
    {
        $this->start = $start;
        $this->end = $end;
        $this->statusId = $statusId;
    }

    public function collection()
    {
        $query = TravelItem::with(['employee.position', 'status'])
            ->whereBetween('date', [$this->start, $this->end]);

        if ($this->statusId) {
            $query->where('travel_status_id', $this->statusId);
        }

        return $query->get()->sortBy('employee.last_name');
    }

    public function headings(): array
    {
        return [
            'Дата',
            'Фамилия Имя Отчество',
            'Должность',
            'Статус',
            'Примечание'
        ];
    }

    public function map($item): array
    {
        return [
            \Carbon\Carbon::parse($item->date)->format('d.m.Y'),
            "{$item->employee->last_name} {$item->employee->first_name} {$item->employee->middle_name}",
            $item->employee->position->name ?? '—',
            $item->status->name ?? $item->status->short_name,
            $item->comment
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Жирный заголовок
        ];
    }
}
