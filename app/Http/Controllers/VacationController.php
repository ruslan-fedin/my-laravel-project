<?php

namespace App\Http\Controllers;

use App\Exports\VacationsColorExport;
use App\Exports\VacationsSummaryExport;
use App\Models\Employee;

use App\Models\Position;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;  // ← ПРАВИЛЬНЫЙ ИМПОРТ


class VacationController extends Controller
{
    /**
     * Страница календаря (классический вид)
     */
    public function index(Request $request)
    {
        $positions = Position::orderBy('name')->get();
        $masters = Employee::whereHas('position', function($query) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%мастер%']);
        })->whereNull('deleted_at')->orderBy('last_name')->get();

        return view('vacations.calendar', compact('positions', 'masters'));
    }

    /**
     * Страница Timeline (горизонтальный вид)
     */
  public function timeline(Request $request)
{
    $year = $request->get('year', now()->year);
    $viewMode = $request->get('view', 'month');

    $positions = Position::orderBy('name')->get();
    $masters = Employee::whereHas('position', function($query) {
        $query->whereRaw('LOWER(name) LIKE ?', ['%мастер%']);
    })->whereNull('deleted_at')->orderBy('last_name')->get();

    // ВАЖНО: Получаем всех сотрудников с отпусками
    $employees = Employee::whereNull('deleted_at')
        ->whereNotNull('vacation_start')
        ->whereNotNull('vacation_end')
        ->with('position')
        ->orderBy('last_name')
        ->get();

    // Для отладки
    \Log::info('Timeline employees:', ['count' => $employees->count()]);

    // Рассчитываем нагрузку по месяцам
    $loadByMonth = [];
    for ($month = 1; $month <= 12; $month++) {
        $monthStart = Carbon::createFromDate($year, $month, 1);
        $monthEnd = $monthStart->copy()->endOfMonth();

        $count = $employees->filter(function($emp) use ($monthStart, $monthEnd) {
            $vacStart = Carbon::parse($emp->vacation_start);
            $vacEnd = Carbon::parse($emp->vacation_end);
            return $vacStart <= $monthEnd && $vacEnd >= $monthStart;
        })->count();

        $total = Employee::whereNull('deleted_at')->where('is_active', true)->count();
        $loadByMonth[] = [
            'month' => $month,
            'month_name' => $monthStart->translatedFormat('M'),
            'count' => $count,
            'total' => $total,
            'percent' => $total > 0 ? round(($count / $total) * 100) : 0,
        ];
    }

    return view('vacations.timeline', compact('employees', 'loadByMonth', 'positions', 'masters', 'year', 'viewMode'));
}
    /**
     * API для классического календаря
     */
    public function api(Request $request)
    {
        $query = Employee::whereNull('deleted_at')
            ->whereNotNull('vacation_start')
            ->whereNotNull('vacation_end')
            ->with('position');

        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        if ($request->filled('master_id')) {
            $query->where('parent_id', $request->master_id);
        }

        if ($request->filled('vacation_type')) {
            $query->where('vacation_type', $request->vacation_type);
        }

        $employees = $query->get();

        $events = [];
        foreach ($employees as $emp) {
            $color = '#3b82f6';

            if ($emp->vacation_type === 'sick') {
                $color = '#ef4444';
            } elseif ($emp->vacation_type === 'unpaid') {
                $color = '#f59e0b';
            } elseif ($emp->vacation_type === 'study') {
                $color = '#8b5cf6';
            }

            $fullName = trim("{$emp->last_name} {$emp->first_name} {$emp->middle_name}");
            $positionName = $emp->position ? $emp->position->name : 'Без должности';

            $events[] = [
                'id' => $emp->id,
                'title' => $fullName,
                'start' => $emp->vacation_start,
                'end' => $emp->vacation_end,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'full_name' => $fullName,
                    'position' => $positionName,
                    'type' => $emp->vacation_type ?: 'Ежегодный',
                    'phone' => $emp->phone ?: '',
                ],
                'url' => route('vacations.edit', $emp->id),
            ];
        }

        return response()->json($events);
    }

    /**
     * API для Timeline
     */
    public function timelineApi(Request $request)
    {
        $year = $request->get('year', now()->year);
        $startDate = Carbon::createFromDate($year, 1, 1);
        $endDate = Carbon::createFromDate($year, 12, 31);

        $query = Employee::whereNull('deleted_at')
            ->whereNotNull('vacation_start')
            ->whereNotNull('vacation_end')
            ->whereBetween('vacation_start', [$startDate, $endDate])
            ->with('position');

        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        $employees = $query->orderBy('last_name')->get();

        $timeline = [];
        foreach ($employees as $emp) {
            $color = '#3b82f6';
            $typeLabel = 'Ежегодный';

            if ($emp->vacation_type === 'sick') {
                $color = '#ef4444';
                $typeLabel = 'Больничный';
            } elseif ($emp->vacation_type === 'unpaid') {
                $color = '#f59e0b';
                $typeLabel = 'За свой счёт';
            } elseif ($emp->vacation_type === 'study') {
                $color = '#8b5cf6';
                $typeLabel = 'Учебный';
            }

            $startDate = Carbon::parse($emp->vacation_start);
            $endDate = Carbon::parse($emp->vacation_end);
            $days = $startDate->diffInDays($endDate) + 1;

            $timeline[] = [
                'id' => $emp->id,
                'employee_id' => $emp->id,
                'name' => trim("{$emp->last_name} {$emp->first_name} {$emp->middle_name}"),
                'position' => $emp->position ? $emp->position->name : 'Без должности',
                'start' => $emp->vacation_start,
                'end' => $emp->vacation_end,
                'start_day' => $startDate->dayOfYear,
                'end_day' => $endDate->dayOfYear,
                'days' => $days,
                'color' => $color,
                'type' => $emp->vacation_type ?: 'annual',
                'type_label' => $typeLabel,
                'edit_url' => route('vacations.edit', $emp->id),
            ];
        }

        // Рассчитываем нагрузку по месяцам
        $loadByMonth = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::createFromDate($year, $month, 1);
            $monthEnd = $monthStart->copy()->endOfMonth();

            $count = $employees->filter(function($emp) use ($monthStart, $monthEnd) {
                $vacStart = Carbon::parse($emp->vacation_start);
                $vacEnd = Carbon::parse($emp->vacation_end);
                return $vacStart <= $monthEnd && $vacEnd >= $monthStart;
            })->count();

            $total = Employee::whereNull('deleted_at')->where('is_active', true)->count();
            $loadByMonth[] = [
                'month' => $month,
                'month_name' => $monthStart->translatedFormat('M'),
                'count' => $count,
                'total' => $total,
                'percent' => $total > 0 ? round(($count / $total) * 100) : 0,
            ];
        }

        return response()->json([
            'timeline' => $timeline,
            'loadByMonth' => $loadByMonth,
            'year' => $year,
        ]);
    }

    /**
     * Форма создания отпуска
     */
    public function create()
    {
        $employees = Employee::whereNull('deleted_at')
            ->where('is_active', true)
            ->orderBy('last_name')
            ->get();

        $positions = Position::orderBy('name')->get();

        return view('vacations.create', compact('employees', 'positions'));
    }

    /**
     * Сохранение нового отпуска
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'vacation_start' => 'required|date',
            'vacation_end' => 'required|date|after_or_equal:vacation_start',
            'vacation_type' => 'required|in:annual,sick,unpaid,study',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $employee->vacation_start = $request->vacation_start;
        $employee->vacation_end = $request->vacation_end;
        $employee->vacation_type = $request->vacation_type;
        $employee->save();

        return redirect()->route('vacations.timeline')
            ->with('success', "Отпуск для {$employee->last_name} {$employee->first_name} добавлен!");
    }

    /**
     * Форма редактирования отпуска
     */
    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        $employees = Employee::whereNull('deleted_at')
            ->where('is_active', true)
            ->orderBy('last_name')
            ->get();

        $positions = Position::orderBy('name')->get();

        return view('vacations.edit', compact('employee', 'employees', 'positions'));
    }

    /**
     * Обновление отпуска
     */
    public function updateVacation(Request $request, $id)
    {
        $request->validate([
            'vacation_start' => 'required|date',
            'vacation_end' => 'required|date|after_or_equal:vacation_start',
            'vacation_type' => 'required|in:annual,sick,unpaid,study',
        ]);

        $employee = Employee::findOrFail($id);
        $employee->vacation_start = $request->vacation_start;
        $employee->vacation_end = $request->vacation_end;
        $employee->vacation_type = $request->vacation_type;
        $employee->save();

        return redirect()->route('vacations.timeline')
            ->with('success', 'Отпуск обновлен!');
    }

    /**
     * Удаление отпуска
     */
    /**
 * Удаление отпуска
 */
public function destroy($id)
{
    $employee = Employee::findOrFail($id);
    $employee->vacation_start = null;
    $employee->vacation_end = null;
    $employee->vacation_type = 'annual';  // ← Установите значение по умолчанию
    $employee->save();

    return redirect()->route('vacations.timeline')
        ->with('success', 'Отпуск удален!');
}

    /**
     * Обновление дат (drag & drop в календаре)
     */
    public function update(Request $request)
    {
        $employee = Employee::findOrFail($request->id);
        $employee->vacation_start = $request->start;
        $employee->vacation_end = $request->end;
        $employee->save();

        return response()->json(['success' => true]);
    }

    /**
     * Экспорт в CSV
     */



    /**
     * Экспорт в Excel
     */

public function export(Request $request)
    {
        $year = $request->get('year', now()->year);
        $filename = 'otpuska_' . $year . '_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new class($year) implements \Maatwebsite\Excel\Concerns\WithMultipleSheets {
            protected $year;

            public function __construct($year)
            {
                $this->year = $year;
            }

            public function sheets(): array
            {
                return [
                    new VacationsSummaryExport($this->year),  // Лист 1: Статистика
                    new VacationsColorExport($this->year),    // Лист 2: Детали
                ];
            }
        }, $filename);
    }


/**
 * Массовое удаление отпусков
 */
public function massDelete(Request $request)
{
    $request->validate([
        'ids' => 'required|array|min:1',
        'ids.*' => 'required|integer|exists:employees,id',
    ]);

    $ids = $request->input('ids');
    $count = 0;

    foreach ($ids as $id) {
        $employee = Employee::findOrFail($id);

        if ($employee->vacation_start && $employee->vacation_end) {
            $employee->vacation_start = null;
            $employee->vacation_end = null;
            $employee->vacation_type = null;
            $employee->save();
            $count++;
        }
    }

    return redirect()->route('vacations.timeline')
        ->with('success', "✅ Удалено отпусков: {$count}");
}


}
