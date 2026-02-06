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
     * Общий список всех сотрудников (ТОЛЬКО ЖИВЫЕ)
     */
    public function index(Request $request)
    {
        // Используем whereNull('deleted_at'), чтобы ГАРАНТИРОВАННО скрыть удаленных
        $employees = Employee::with('position')
            ->whereNull('deleted_at')
            ->when($request->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('last_name', 'LIKE', "%{$search}%")
                      ->orWhere('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('middle_name', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('last_name')
            ->paginate(20);

        return view('employees.index', compact('employees'));
    }

    /**
     * Список удаленных сотрудников (ТОЛЬКО АРХИВ)
     */
   /**
     * Список удаленных сотрудников (Архив)
     * ГАРАНТИРОВАННО выводит только тех, у кого заполнена дата удаления.
     */
  public function archive()
{
    // Теперь мы точно знаем, что эти данные есть
    $employees = Employee::onlyTrashed()
        ->with('position')
        ->orderBy('deleted_at', 'desc')
        ->get();

    return view('employees.archive', compact('employees'));
}
    /**
     * СОХРАНЕНИЕ ИЗМЕНЕНИЙ
     */
public function update(Request $request, $id)
{
    $employee = \App\Models\Employee::findOrFail($id);

    $request->validate([
        'last_name'  => 'required|string|max:255',
        'first_name' => 'required|string|max:255',
        'photo'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    if ($request->hasFile('photo')) {
        // 1. Удаляем старое фото из папки public/uploads/employees
        if ($employee->photo && file_exists(public_path($employee->photo))) {
            unlink(public_path($employee->photo));
        }

        // 2. Генерируем уникальное имя файла
        $fileName = time() . '.' . $request->photo->extension();

        // 3. Перемещаем файл НАПРЯМУЮ в public/uploads/employees
        $request->photo->move(public_path('uploads/employees'), $fileName);

        // 4. Записываем в базу чистый путь
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
     * Удаление сотрудника (Soft Delete)
     */
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete(); // Устанавливает deleted_at

        return redirect()->route('employees.index')
            ->with('success', "Сотрудник {$employee->last_name} перемещен в архив.");
    }

    /**
     * Восстановление сотрудника
     */
    public function restore($id)
    {
        $employee = Employee::withTrashed()->findOrFail($id);
        $employee->restore(); // Очищает deleted_at

        return redirect()->route('employees.archive')
            ->with('success', "Сотрудник {$employee->last_name} восстановлен.");
    }

    /**
     * Метод просмотра (show)
     */
    public function show($id)
    {
        $employee = Employee::withTrashed()->with(['position', 'leader'])->findOrFail($id);

        $movements = collect(); $history = collect(); $documents = collect(); $trainings = collect();
        return view('employees.show', compact('employee', 'movements', 'history', 'documents', 'trainings'));
    }

    /**
     * Метод редактирования (edit)
     */
    public function edit($id)
    {
        $employee = Employee::withTrashed()->findOrFail($id);
        $positions = Position::orderBy('name')->get();
        $leaders = Employee::whereNull('deleted_at')->where('id', '!=', $id)->get();

        return view('employees.edit', compact('employee', 'positions', 'leaders'));
    }

    /**
     * Остальные методы бригад (без изменений)
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
    return view('employees.import');
}


public function forceDelete($id)
{
    $employee = Employee::withTrashed()->findOrFail($id);
    $employee->forceDelete(); // Это физическое удаление из таблицы

    return back()->with('success', 'Сотрудник полностью удален из системы.');
}

public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv'
    ]);

    $data = \Maatwebsite\Excel\Facades\Excel::toArray([], $request->file('file'));
    $rows = $data[0];
    $imported = 0;

    // Получаем ID должности по умолчанию (например, "Рабочий"), чтобы не было пустоты
    $defaultPosition = \App\Models\Position::firstOrCreate(['name' => 'Рабочий ОЗХ']);

    foreach ($rows as $row) {
        // 1. Ищем ФИО (обычно 2 или 3 колонка)
        $fullName = trim($row[2] ?? $row[1] ?? '');

        // 2. Ищем название должности (обычно 1 или 2 колонка, например "раб.")
        $posName = trim($row[1] ?? $row[0] ?? '');

        if (empty($fullName) || in_array($fullName, ['ФИО', 'Итого', '№', 'Период'])) continue;

        $parts = explode(' ', $fullName);
        if (count($parts) < 2) continue;

        // Ищем должность в базе по названию из файла
        // Если в файле "раб.", а в базе "Рабочий", можно добавить проверку через match
        $positionId = $defaultPosition->id;
        if (!empty($posName)) {
            $foundPosition = \App\Models\Position::where('name', 'like', '%' . $posName . '%')->first();
            if ($foundPosition) {
                $positionId = $foundPosition->id;
            }
        }

        $exists = \App\Models\Employee::where('last_name', $parts[0])
            ->where('first_name', $parts[1])
            ->exists();

        if (!$exists) {
            \App\Models\Employee::create([
                'last_name'   => $parts[0],
                'first_name'  => $parts[1],
                'middle_name' => $parts[2] ?? '',
                'position_id' => $positionId, // Теперь здесь будет ID, а не пустота
                'is_active'   => true,
            ]);
            $imported++;
        }
    }

    return redirect()->route('employees.index')->with('success', "Импортировано: $imported. Должности назначены.");
}
}
