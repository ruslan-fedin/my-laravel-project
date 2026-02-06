<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Status;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class SharedBoardController extends Controller
{
    /**
     * Отображение публичной доски табеля.
     * Ссылка теперь строго привязана к ID табеля, чтобы данные не перемешивались.
     */
    public function showBoard($secret)
    {
        // 1. Пытаемся найти конкретный табель по ID
        $timesheet = DB::table('travel_timesheets')
            ->where('id', $secret)
            ->first();

        // 2. Если по ID не нашли (например, в ссылке старый текст 'grafik2026'),
        // тогда берем самый свежий, чтобы не показывать ошибку 404 сразу
        if (!$timesheet) {
            $timesheet = DB::table('travel_timesheets')->orderBy('id', 'desc')->first();
        }

        // Если в базе вообще нет табелей
        if (!$timesheet) {
            abort(404, "Табель не найден в системе.");
        }

        // 3. Автоматическое определение таблицы деталей (items или details)
        $detailTable = Schema::hasTable('travel_timesheet_items') ? 'travel_timesheet_items' : 'travel_timesheet_details';

        // 4. Загружаем сотрудников, добавленных именно в этот табель
        // Соблюдаем правило: ФИО полностью через модель Employee
        $employees = Employee::whereIn('id', function($query) use ($timesheet, $detailTable) {
            $query->select('employee_id')
                  ->from($detailTable)
                  ->where('travel_timesheet_id', $timesheet->id);
        })
        ->whereNull('deleted_at')
        ->get()
        ->sortBy('last_name');

        // 5. Загружаем статусы и индексируем их по ID для быстрого поиска
        $statuses = Status::all()->keyBy('id');

        // 6. Получаем ячейки табеля (отметки присутствия)
        $itemsRaw = DB::table($detailTable)
            ->where('travel_timesheet_id', $timesheet->id)
            ->get();

        $items = [];
        foreach ($itemsRaw as $item) {
            // Группируем данные для удобного вывода в Blade: $items[сотрудник][дата]
            $items[$item->employee_id][$item->date] = $item;
        }

        // 7. Формируем массив дат периода табеля
        $start = Carbon::parse($timesheet->start_date);
        $end = Carbon::parse($timesheet->end_date);
        $dates = [];

        for ($d = $start->copy(); $d <= $end; $d->addDay()) {
            $dates[] = $d->copy();
        }

        // 8. Возвращаем вид с правильными данными
        return view('public.tabel-view', compact('timesheet', 'employees', 'statuses', 'items', 'dates'));
    }
}
