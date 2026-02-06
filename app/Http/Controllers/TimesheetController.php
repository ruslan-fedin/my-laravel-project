<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use App\Models\Status;
use App\Models\Employee;
use App\Models\Timesheet;
use App\Models\TimesheetItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\TimesheetExport;
use Maatwebsite\Excel\Facades\Excel;

class TimesheetController extends Controller
{
    // 1. ИНДЕКС (СПИСОК ТАБЕЛЕЙ)
    public function index()
    {
        $timesheets = Timesheet::orderBy('created_at', 'desc')->get();
        return view('timesheets.index', compact('timesheets'));
    }

    // 2. ПРОСМОТР ТАБЕЛЯ (ГЛАВНЫЙ ЭКРАН)
    public function show(Timesheet $timesheet, Request $request)
    {
        $employeeIds = TimesheetItem::where('timesheet_id', $timesheet->id)
            ->distinct()
            ->pluck('employee_id');

        // ФИО полностью (last_name first_name middle_name)
        $employees = Employee::whereIn('id', $employeeIds)
            ->with('position')
            ->orderBy('last_name')
            ->get();

        $statuses = Status::all();

        // Группировка: [ID][DATE]
        $items = TimesheetItem::where('timesheet_id', $timesheet->id)
            ->get()
            ->groupBy('employee_id')
            ->map(function ($employeeItems) {
                return $employeeItems->keyBy('date');
            });

        $dates = $this->getDates($timesheet);

        return view('timesheets.show', compact('timesheet', 'employees', 'statuses', 'items', 'dates'));
    }

    // 3. СОХРАНЕНИЕ ЯЧЕЙКИ (AJAX)
    public function saveItem(Request $request)
    {
        TimesheetItem::updateOrCreate(
            [
                'timesheet_id' => $request->timesheet_id,
                'employee_id'  => $request->employee_id,
                'date'         => $request->date,
            ],
            ['status_id' => $request->status_id ?: null]
        );
        return response()->json(['success' => true]);
    }

    // 4. СОХРАНЕНИЕ КОММЕНТАРИЯ (AJAX)
    public function saveComment(Request $request)
    {
        TimesheetItem::where('timesheet_id', $request->timesheet_id)
            ->where('employee_id', $request->employee_id)
            ->update(['comment' => $request->comment]);

        return response()->json(['success' => true]);
    }

    // 5. МАССОВОЕ ЗАПОЛНЕНИЕ
    public function massUpdate(Request $request, Timesheet $timesheet)
    {
        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $statusId = $request->status_id;
        $employeeId = $request->employee_id;

        $targetIds = ($employeeId && $employeeId !== 'all')
            ? [$employeeId]
            : TimesheetItem::where('timesheet_id', $timesheet->id)->distinct()->pluck('employee_id')->toArray();

        foreach ($targetIds as $empId) {
            $current = $start->copy();
            while ($current <= $end) {
                $dateStr = $current->format('Y-m-d');
                $item = TimesheetItem::where(['timesheet_id' => $timesheet->id, 'employee_id' => $empId, 'date' => $dateStr])->first();

                if (!$item) {
                    if (!empty($statusId)) {
                        TimesheetItem::create(['timesheet_id' => $timesheet->id, 'employee_id' => $empId, 'date' => $dateStr, 'status_id' => $statusId]);
                    }
                } else {
                    if (empty($statusId)) {
                        $item->update(['status_id' => null]);
                    } elseif (is_null($item->status_id) || $item->status_id == 0) {
                        $item->update(['status_id' => $statusId]);
                    }
                }
                $current->addDay();
            }
        }
        return back()->with('success', 'Готово');
    }

    // 6. ДОБАВЛЕНИЕ СОТРУДНИКА
    public function addEmployee(Request $request, Timesheet $timesheet)
    {
        TimesheetItem::firstOrCreate([
            'timesheet_id' => $timesheet->id,
            'employee_id'  => $request->employee_id,
            'date'         => $timesheet->start_date,
        ]);
        return back();
    }

    // 7. ЗАПОЛНИТЬ ВСЕМИ АКТИВНЫМИ
    public function fillActive(Timesheet $timesheet)
    {
        $activeEmployees = Employee::where('is_active', true)->get();
        foreach ($activeEmployees as $emp) {
            TimesheetItem::firstOrCreate([
                'timesheet_id' => $timesheet->id,
                'employee_id'  => $emp->id,
                'date'         => $timesheet->start_date,
            ]);
        }
        return back()->with('success', 'Сотрудники добавлены');
    }

    // 8. УДАЛЕНИЕ СОТРУДНИКА ИЗ ТАБЕЛЯ
    public function removeEmployee(Timesheet $timesheet, Employee $employee)
    {
        TimesheetItem::where('timesheet_id', $timesheet->id)
            ->where('employee_id', $employee->id)
            ->delete();
        return back()->with('success', 'Сотрудник удален из табеля');
    }

    // 9. СОЗДАНИЕ И РЕДАКТИРОВАНИЕ ТАБЕЛЯ
    public function create() { return view('timesheets.create'); }


    public function edit(Timesheet $timesheet) { return view('timesheets.edit', compact('timesheet')); }


    public function destroy(Timesheet $timesheet)
    {
        $timesheet->delete();
        return redirect()->route('timesheets.index');
    }

    // 10. ЭКСПОРТ (EXCEL / PDF)
    public function exportExcel(Timesheet $timesheet)
    {
        Carbon::setLocale('ru');
        $dates = $this->getDates($timesheet);
        $employees = $this->getEmployeesForExport($timesheet);
        return Excel::download(new TimesheetExport($timesheet, $employees, $dates), "timesheet_{$timesheet->id}.xlsx");
    }

    public function exportPdf(Timesheet $timesheet)
    {
        Carbon::setLocale('ru');
        $dates = $this->getDates($timesheet);
        $employees = $this->getEmployeesForExport($timesheet);
        $items = TimesheetItem::where('timesheet_id', $timesheet->id)->get()->groupBy('employee_id');
        $statuses = Status::all();

        $pdf = Pdf::loadView('exports.timesheet_pdf', compact('timesheet', 'employees', 'dates', 'items', 'statuses'));
        return $pdf->setPaper('a4', 'landscape')->download("timesheet_{$timesheet->id}.pdf");
    }

    // Вспомогательные методы
    private function getDates($timesheet) {
        $start = Carbon::parse($timesheet->start_date);
        $end = Carbon::parse($timesheet->end_date);
        $dates = [];
        while ($start <= $end) { $dates[] = $start->copy(); $start->addDay(); }
        return $dates;
    }

    private function getEmployeesForExport($timesheet) {
        $employeeIds = TimesheetItem::where('timesheet_id', $timesheet->id)->pluck('employee_id')->unique();
        return Employee::whereIn('id', $employeeIds)->with('position')->get()->sortBy('last_name');
    }




// 9. СОЗДАНИЕ ТАБЕЛЯ
    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date'
        ]);

        $ts = new Timesheet();
        $ts->start_date = $request->start_date;
        $ts->end_date   = $request->end_date;

        // РЕШЕНИЕ ОШИБКИ ПО ПОЛЮ 'date'
        // Если база требует это поле, записываем дату начала периода
        $ts->date = $request->start_date;

        // РЕШЕНИЕ ОШИБКИ ПО ПОЛЮ 'employee_id'
        // Если поле обязательно в БД, привязываем к первому сотруднику
        // или к переданному в запросе
        if ($request->has('employee_id')) {
            $ts->employee_id = $request->employee_id;
        } else {
            $firstEmp = Employee::first();
            $ts->employee_id = $firstEmp ? $firstEmp->id : 1;
        }

        $ts->save();

        return redirect()->route('timesheets.show', $ts->id)->with('success', 'Табель успешно создан');
    }

    // РЕДАКТИРОВАНИЕ ПАРАМЕТРОВ
    public function update(Request $request, Timesheet $timesheet)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date'
        ]);

        $timesheet->update([
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'date'        => $request->start_date, // Синхронизируем лишнее поле
            'employee_id' => $request->employee_id ?? $timesheet->employee_id
        ]);

        return redirect()->route('timesheets.index')->with('success', 'Параметры обновлены');
    }


}
