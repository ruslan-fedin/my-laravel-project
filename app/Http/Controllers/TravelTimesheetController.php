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
    public function index()
    {
        $timesheets = TravelTimesheet::orderBy('start_date', 'desc')->get();
        return view('travel_timesheets.index', compact('timesheets'));
    }

    /**
     * СОЗДАНИЕ ТАБЕЛЯ (Тот самый недостающий метод)
     */
    public function store(Request $request)
{
    $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    // Убираем формирование $title и запись в базу
    $timesheet = TravelTimesheet::create([
        'start_date' => $request->start_date,
        'end_date'   => $request->end_date,
        // Поля 'title' здесь больше нет
    ]);

    return redirect()->route('travel-timesheets.show', $timesheet->id)
                     ->with('success', 'Табель создан');
}

    public function show($id)
    {
        $timesheet = TravelTimesheet::findOrFail($id);

        // Получаем сотрудников, у которых есть хотя бы одна запись в этом табеле
        $employeeIds = TravelTimesheetItem::where('travel_timesheet_id', $id)
            ->distinct()
            ->pluck('employee_id');

        $employees = Employee::whereIn('id', $employeeIds)->get();

        $statuses = Status::all();

        $items = TravelTimesheetItem::where('travel_timesheet_id', $id)
            ->get()
            ->groupBy('employee_id');

        $allAvailableEmployees = Employee::all();

        return view('travel_timesheets.show', compact(
            'timesheet', 'employees', 'items', 'allAvailableEmployees', 'statuses'
        ));
    }

    public function addEmployee(Request $request, $id)
    {
        $employeeId = $request->input('employee_id');
        $timesheet = TravelTimesheet::findOrFail($id);

        if ($employeeId) {
            // Используем модель для единообразия
            TravelTimesheetItem::updateOrCreate([
                'travel_timesheet_id' => $id,
                'employee_id'         => $employeeId,
                'date'                => $timesheet->start_date
            ], [
                // оставляем поля статуса пустыми при инициализации
            ]);
        }
        return redirect()->back();
    }

    public function addAll($id)
    {
        $timesheet = TravelTimesheet::findOrFail($id);
        $allEmployees = Employee::all();

        foreach ($allEmployees as $emp) {
            TravelTimesheetItem::updateOrCreate([
                'travel_timesheet_id' => $id,
                'employee_id'         => $emp->id,
                'date'                => $timesheet->start_date
            ]);
        }
        return redirect()->back();
    }

    public function removeEmployee($id, $empId)
    {
        TravelTimesheetItem::where('travel_timesheet_id', $id)
            ->where('employee_id', $empId)
            ->delete();
        return redirect()->back();
    }

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

    public function updateComment(Request $request, $id)
    {
        // Метод для сохранения примечаний (был в JS, но не было в контроллере)
        TravelTimesheetItem::where('travel_timesheet_id', $id)
            ->where('employee_id', $request->employee_id)
            ->update(['comment' => $request->comment]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $timesheet = TravelTimesheet::findOrFail($id);
        TravelTimesheetItem::where('travel_timesheet_id', $id)->delete();
        $timesheet->delete();

        return redirect()->route('travel-timesheets.index')
                         ->with('success', 'Табель успешно удален');
    }
}
