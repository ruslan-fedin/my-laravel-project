<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VacationController extends Controller
{
    /**
     * Страница календаря
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
     * API для FullCalendar
     */
    public function api(Request $request)
    {
        $startDate = $request->get('start', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end', now()->endOfYear()->format('Y-m-d'));
        $positionId = $request->get('position_id');
        $masterId = $request->get('master_id');

        $query = Employee::whereNull('deleted_at')
            ->whereNotNull('vacation_start')
            ->whereNotNull('vacation_end');

        if ($positionId) {
            $query->where('position_id', $positionId);
        }

        if ($masterId) {
            $query->where('parent_id', $masterId);
        }

        $employees = $query->get();

        $events = [];
        foreach ($employees as $emp) {
            $color = '#3b82f6'; // По умолчанию синий

            // Цвета по типу отпуска
            if ($emp->vacation_type === 'sick') {
                $color = '#ef4444'; // Красный - больничный
            } elseif ($emp->vacation_type === 'unpaid') {
                $color = '#f59e0b'; // Оранжевый - за свой счет
            } elseif ($emp->vacation_type === 'study') {
                $color = '#8b5cf6'; // Фиолетовый - учебный
            }

            $events[] = [
                'id' => $emp->id,
                'title' => $emp->last_name . ' ' . mb_substr($emp->first_name, 0, 1) . '.',
                'start' => $emp->vacation_start,
                'end' => $emp->vacation_end,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'full_name' => $emp->last_name . ' ' . $emp->first_name . ' ' . $emp->middle_name,
                    'position' => $emp->position->name ?? 'Без должности',
                    'type' => $emp->vacation_type ?? 'Ежегодный',
                    'phone' => $emp->phone ?? '',
                ],
                'url' => route('employees.edit', $emp->id),
            ];
        }

        return response()->json($events);
    }

    /**
     * Обновление дат отпуска (drag & drop)
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
     * Экспорт в Excel
     */
    public function export(Request $request)
    {
        // Простая реализация - можно улучшить с Maatwebsite/Excel
        $employees = Employee::whereNull('deleted_at')
            ->whereNotNull('vacation_start')
            ->whereNotNull('vacation_end')
            ->with('position')
            ->get();

        $csv = "ФИО,Должность,Начало,Конец,Дней,Тип\n";
        foreach ($employees as $emp) {
            $days = Carbon::parse($emp->vacation_start)->diffInDays(Carbon::parse($emp->vacation_end));
            $csv .= "{$emp->last_name} {$emp->first_name} {$emp->middle_name},";
            $csv .= "{$emp->position->name ?? 'Без должности'},";
            $csv .= "{$emp->vacation_start},{$emp->vacation_end},{$days},";
            $csv .= "{$emp->vacation_type ?? 'Ежегодный'}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="otpuska_' . now()->format('Y-m-d') . '.csv"');
    }
}
