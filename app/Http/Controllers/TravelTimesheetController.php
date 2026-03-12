<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TravelTimesheet;
use App\Models\Employee;
use App\Models\Status;
use App\Models\TravelTimesheetItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TravelTimesheetController extends Controller
{
    /**
     * Главная страница: список табелей
     */
    public function index()
    {
        $timesheets = TravelTimesheet::orderBy('start_date', 'desc')->get();
        return view('travel_timesheets.index', compact('timesheets'));
    }

    /**
     * Создание нового табеля
     */
    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $timesheet = TravelTimesheet::create([
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
        ]);

        return redirect()->route('travel-timesheets.show', $timesheet->id)
                         ->with('success', 'Табель создан');
    }

    /**
     * Просмотр конкретного табеля
     */
    public function show($id)
    {
        $timesheet = TravelTimesheet::findOrFail($id);

        // Сотрудники, которые уже в этом табеле (независимо от текущей активности)
        $employeeIds = TravelTimesheetItem::where('travel_timesheet_id', $id)
            ->distinct()
            ->pluck('employee_id');

        $employees = Employee::whereIn('id', $employeeIds)
            ->orderBy('last_name')
            ->get();

        $statuses = Status::all();

        $items = TravelTimesheetItem::where('travel_timesheet_id', $id)
            ->get()
            ->groupBy('employee_id');

        // ИСПРАВЛЕНО: Для выбора по одному берем ТОЛЬКО АКТИВНЫХ
        $allAvailableEmployees = Employee::where('is_active', true)
            ->orderBy('last_name')
            ->get();

        return view('travel_timesheets.show', compact(
            'timesheet', 'employees', 'items', 'allAvailableEmployees', 'statuses'
        ));
    }

    /**
     * Добавление одного сотрудника (только если активен)
     */
    public function addEmployee(Request $request, $id)
    {
        $employeeId = $request->input('employee_id');
        $timesheet = TravelTimesheet::findOrFail($id);

        if ($employeeId) {
            $emp = Employee::find($employeeId);
            // Дополнительная проверка на активность на стороне сервера
            if ($emp && $emp->is_active) {
                TravelTimesheetItem::updateOrCreate([
                    'travel_timesheet_id' => $id,
                    'employee_id'         => $employeeId,
                    'date'                => $timesheet->start_date
                ]);
            }
        }
        return redirect()->back();
    }

    /**
     * Добавление ВСЕХ АКТИВНЫХ сотрудников
     */
    public function addAll($id)
    {
        $timesheet = TravelTimesheet::findOrFail($id);

        // ИСПРАВЛЕНО: Фильтруем по полю is_active
        $activeEmployees = Employee::where('is_active', true)->get();

        foreach ($activeEmployees as $emp) {
            TravelTimesheetItem::updateOrCreate([
                'travel_timesheet_id' => $id,
                'employee_id'         => $emp->id,
                'date'                => $timesheet->start_date
            ]);
        }
        return redirect()->back()->with('success', 'Добавлены только активные сотрудники');
    }

    /**
     * Удаление сотрудника из табеля
     */
    public function removeEmployee($id, $empId)
    {
        TravelTimesheetItem::where('travel_timesheet_id', $id)
            ->where('employee_id', $empId)
            ->delete();
        return redirect()->back();
    }

    /**
     * Обновление статуса в ячейке (AJAX)
     */
    public function updateStatus(Request $request, $id)
    {
        TravelTimesheetItem::updateOrCreate(
            [
                'travel_timesheet_id' => $id,
                'employee_id'         => $request->employee_id,
                'date'                => $request->date,
            ],
            [
                'status_id'           => $request->status_id ?: null,
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Обновление примечания сотрудника (AJAX)
     */
    public function updateComment(Request $request, $id)
    {
        TravelTimesheetItem::where('travel_timesheet_id', $id)
            ->where('employee_id', $request->employee_id)
            ->update(['comment' => $request->comment]);

        return response()->json(['success' => true]);
    }

    /**
     * Удаление всего табеля
     */
    public function destroy($id)
    {
        $timesheet = TravelTimesheet::findOrFail($id);
        TravelTimesheetItem::where('travel_timesheet_id', $id)->delete();
        $timesheet->delete();

        return redirect()->route('travel-timesheets.index')
                         ->with('success', 'Табель полностью удален');
    }

    /**
     * Форма редактирования дат табеля
     */
    public function edit(TravelTimesheet $travelTimesheet)
    {
        return view('travel_timesheets.edit', [
            'timesheet' => $travelTimesheet
        ]);
    }

    /**
     * Сохранение обновленных дат табеля
     */
    public function update(Request $request, TravelTimesheet $travelTimesheet)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'note'       => 'nullable|string|max:255',
        ]);

        $travelTimesheet->update($validated);

        return redirect()->route('travel-timesheets.index')
                         ->with('success', 'Сроки табеля обновлены');
    }
}
