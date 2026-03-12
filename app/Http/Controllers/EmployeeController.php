<?php

namespace App\Http\Controllers;

use Storage;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    /**
     * Вспомогательный метод для получения мастеров
     */
    private function getMastersData()
    {
        return Employee::whereHas('position', function($query) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%мастер%']);
        })->whereNull('deleted_at')->orderBy('last_name')->get();
    }

    /**
     * ✅ ИСПРАВЛЕНО: Список АКТИВНЫХ сотрудников
     */
    public function index(Request $request)
    {
        $employees = Employee::with('position')
            ->where('is_active', true)      // ✅ Только активные
            ->whereNull('deleted_at')       // ✅ Не удалённые
            ->when($request->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('last_name', 'LIKE', "%{$search}%")
                      ->orWhere('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('middle_name', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('last_name')
            ->paginate(20);

        $masters = $this->getMastersData();
        return view('employees.index', compact('employees', 'masters'));
    }

    /**
     * ✅ ИСПРАВЛЕНО: Список НЕАКТИВНЫХ сотрудников (Архив)
     */
    public function archive()
    {
        $employees = Employee::with('position')
            ->where('is_active', false)     // ✅ Только неактивные
            ->whereNull('deleted_at')       // ✅ Не удалённые окончательно
            ->orderBy('last_name')
            ->paginate(20);

        $masters = $this->getMastersData();
        return view('employees.archive', compact('employees', 'masters'));
    }

    /**
     * ✅ НОВЫЙ МЕТОД: Активировать сотрудника из архива
     */
    public function activate($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->is_active = true;
        $employee->status = 'active';
        $employee->save();

        return redirect()->route('employees.archive')
            ->with('success', "✅ Сотрудник {$employee->last_name} активирован");
    }

    /**
     * ФОРМА СОЗДАНИЯ
     */
    public function create()
    {
        $positions = Position::orderBy('name')->get();
        $masters = $this->getMastersData();
        return view('employees.create', compact('positions', 'masters'));
    }

    /**
     * СОХРАНЕНИЕ НОВОГО СОТРУДНИКА
     */
    public function store(Request $request)
    {
        $request->validate([
            'last_name'  => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'photo'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $employee = new Employee();

        if ($request->hasFile('photo')) {
            $fileName = time() . '.' . $request->photo->extension();
            $request->photo->move(public_path('uploads/employees'), $fileName);
            $employee->photo = 'uploads/employees/' . $fileName;
        }

        $employee->last_name   = $request->last_name;
        $employee->first_name  = $request->first_name;
        $employee->middle_name = $request->middle_name;
        $employee->position_id = $request->position_id;
        $employee->is_active   = true;
        $employee->status      = 'active';
        $employee->save();

        return redirect()->route('employees.index')->with('success', 'Сотрудник успешно создан');
    }

    /**
     * ✅ ИЗМЕНЕНО: Удаление = деактивация (is_active = false)
     */
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->is_active = false;
        $employee->status = 'fired';
        $employee->save();

        return redirect()->route('employees.index')
            ->with('success', "Сотрудник {$employee->last_name} перемещен в архив");
    }

    /**
     * ✅ ИЗМЕНЕНО: Восстановление = активация
     */
    public function restore($id)
    {
        return $this->activate($id);
    }

    /**
     * Полное удаление (навсегда)
     */
    public function forceDelete($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->forceDelete();

        return redirect()->route('employees.archive')
            ->with('success', 'Сотрудник полностью удален из системы');
    }

    /**
     * Метод просмотра (show)
     */
    public function show(Employee $employee)
    {
        $masters = $this->getMastersData();
        return view('employees.show', compact('employee', 'masters'));
    }

    /**
     * Метод редактирования (edit)
     */
    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        $positions = Position::orderBy('name')->get();
        $leaders = Employee::whereNull('deleted_at')->where('id', '!=', $id)->get();
        $masters = $this->getMastersData();

        return view('employees.edit', compact('employee', 'positions', 'leaders', 'masters'));
    }

    /**
     * СОХРАНЕНИЕ ИЗМЕНЕНИЙ (UPDATE)
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'last_name'  => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'photo'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($employee->photo && file_exists(public_path($employee->photo))) {
                unlink(public_path($employee->photo));
            }
            $fileName = time() . '.' . $request->photo->extension();
            $request->photo->move(public_path('uploads/employees'), $fileName);
            $employee->photo = 'uploads/employees/' . $fileName;
        }

        $employee->last_name   = $request->last_name;
        $employee->first_name  = $request->first_name;
        $employee->middle_name = $request->middle_name;
        $employee->position_id = $request->position_id;
        $employee->birth_date  = $request->birth_date;
        $employee->hire_date   = $request->hire_date;
        $employee->phone       = $request->phone;
        $employee->is_active   = $request->is_active;
        $employee->status      = $request->is_active ? 'active' : 'fired';
        $employee->save();

        return redirect()->route('employees.index')->with('success', 'Данные обновлены');
    }

    /**
     * Отображение структуры бригад
     */
    public function showBrigades()
    {
        $masterIds = Position::whereRaw('LOWER(name) LIKE ?', ['%мастер%'])->pluck('id')->toArray();
        $brigadierIds = Position::whereRaw('LOWER(name) LIKE ?', ['%бриг%'])->pluck('id')->toArray();

        $masters = Employee::whereIn('position_id', $masterIds)->whereNull('deleted_at')->orderBy('last_name')->get();
        $allBrigadiers = Employee::whereIn('position_id', $brigadierIds)->whereNull('deleted_at')->with(['subordinates'])->get();
        $freeWorkers = Employee::whereNotIn('position_id', array_merge($masterIds, $brigadierIds))->whereNull('deleted_at')->get();
        $allLeaders = Employee::whereIn('position_id', array_merge($masterIds, $brigadierIds))->whereNull('deleted_at')->get();

        $masterIdsArr = $masters->pluck('id')->toArray();
        $orphanBrigadiers = $allBrigadiers->filter(fn($br) => !in_array($br->parent_id, $masterIdsArr));

        return view('brigades.index', compact('masters', 'allBrigadiers', 'orphanBrigadiers', 'freeWorkers', 'allLeaders'));
    }

    public function updateLeader(Request $request)
    {
        $employee = Employee::findOrFail($request->employee_id);
        $employee->parent_id = $request->parent_id ?: null;
        $employee->save();
        return back();
    }

    public function startVacation(Request $request)
    {
        $brigadier = Employee::findOrFail($request->absentee_id);
        $brigadier->status = 'vacation';
        $brigadier->substitute_id = $request->substitute_id;
        $brigadier->save();
        return back();
    }

    public function returnVacation(Request $request)
    {
        $brigadier = Employee::findOrFail($request->brigadier_id);
        $brigadier->status = 'active';
        $brigadier->substitute_id = null;
        $brigadier->save();
        return back();
    }

    public function updateLocation(Request $request)
    {
        DB::table('brigade_locations')->updateOrInsert(
            ['brigadier_id' => $request->brigadier_id],
            ['location_name' => $request->location_name, 'updated_at' => now()]
        );
        return response()->json(['success' => true]);
    }

    public function showImportForm()
    {
        $masters = $this->getMastersData();
        return view('employees.import', compact('masters'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $data = Excel::toArray([], $request->file('file'));
        $rows = $data[0];
        $imported = 0;

        $defaultPosition = Position::firstOrCreate(['name' => 'Рабочий ОЗХ']);

        foreach ($rows as $row) {
            $fullName = trim($row[2] ?? $row[1] ?? '');
            $posName = trim($row[1] ?? $row[0] ?? '');

            if (empty($fullName) || in_array($fullName, ['ФИО', 'Итого', '№', 'Период'])) continue;

            $parts = explode(' ', $fullName);
            if (count($parts) < 2) continue;

            $positionId = $defaultPosition->id;
            if (!empty($posName)) {
                $foundPosition = Position::where('name', 'like', '%' . $posName . '%')->first();
                if ($foundPosition) {
                    $positionId = $foundPosition->id;
                }
            }

            $exists = Employee::where('last_name', $parts[0])
                ->where('first_name', $parts[1])
                ->exists();

            if (!$exists) {
                Employee::create([
                    'last_name'   => $parts[0],
                    'first_name'  => $parts[1],
                    'middle_name' => $parts[2] ?? '',
                    'position_id' => $positionId,
                    'is_active'   => true,
                    'status'      => 'active'
                ]);
                $imported++;
            }
        }

        return redirect()->route('employees.index')->with('success', "Импортировано: $imported. Должности назначены.");
    }
}
