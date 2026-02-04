<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    /**
     * Список активных сотрудников
     */
    public function index()
    {
        $employees = Employee::active()
            ->with('position')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(20);

        return view('employees.index', compact('employees'));
    }

    /**
     * Форма создания сотрудника
     */
    public function create()
    {
        $positions = Position::orderBy('name')->get();
        return view('employees.create', compact('positions'));
    }

    /**
     * Сохранение нового сотрудника
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'last_name' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'position_id' => 'required|exists:positions,id',
            'birth_date' => 'nullable|date',
            'hire_date' => 'nullable|date',
            'phone' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $employee = Employee::create($validated);

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Сотрудник успешно добавлен.');
    }

    /**
     * Просмотр сотрудника
     */
    public function show(Employee $employee)
    {
        // Загружаем даже удаленных сотрудников
        $employee->load(['position', 'timesheetItems' => function($query) {
            $query->latest()->limit(50);
        }]);

        return view('employees.show', compact('employee'));
    }

    /**
     * Форма редактирования
     */
    public function edit(Employee $employee)
    {
        $positions = Position::orderBy('name')->get();
        return view('employees.edit', compact('employee', 'positions'));
    }

    /**
     * Обновление сотрудника
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'last_name' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'position_id' => 'required|exists:positions,id',
            'birth_date' => 'nullable|date',
            'hire_date' => 'nullable|date',
            'phone' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $employee->update($validated);

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Данные сотрудника обновлены.');
    }

    /**
     * Мягкое удаление (в архив)
     */
    public function destroy(Employee $employee)
    {
        try {
            DB::beginTransaction();

            // Сохраняем имя для сообщения
            $employeeName = $employee->full_name;

            // Мягкое удаление
            $employee->delete();

            DB::commit();

            return redirect()->route('employees.index')
                ->with('success', "Сотрудник \"{$employeeName}\" перемещен в архив. Записи в табеле сохранены.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при удалении сотрудника: ' . $e->getMessage());

            return back()->with('error', 'Произошла ошибка при удалении сотрудника.');
        }
    }

    /**
     * Список удаленных сотрудников (архив)
     */
    public function trashed()
    {
        $employees = Employee::onlyTrashed()
            ->with('position')
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        return view('employees.trashed', compact('employees'));
    }

    /**
     * Восстановление сотрудника из архива
     */
    public function restore($id)
    {
        $employee = Employee::onlyTrashed()->findOrFail($id);

        try {
            DB::beginTransaction();

            $employee->restore();

            DB::commit();

            return redirect()->route('employees.trashed')
                ->with('success', "Сотрудник \"{$employee->full_name}\" восстановлен из архива.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при восстановлении сотрудника: ' . $e->getMessage());

            return back()->with('error', 'Произошла ошибка при восстановлении сотрудника.');
        }
    }

    /**
     * Полное удаление из архива
     */
    public function forceDelete($id)
    {
        $employee = Employee::onlyTrashed()->findOrFail($id);

        try {
            // Проверяем наличие записей в табеле
            if ($employee->timesheetItems()->exists()) {
                return back()->with('error',
                    'Невозможно удалить сотрудника, так как имеются связанные записи в табеле. ' .
                    'Сначала удалите или переназначьте записи в табеле.');
            }

            $employeeName = $employee->full_name;
            $employee->forceDelete();

            return redirect()->route('employees.trashed')
                ->with('success', "Сотрудник \"{$employeeName}\" полностью удален из системы.");

        } catch (\Exception $e) {
            Log::error('Ошибка при полном удалении сотрудника: ' . $e->getMessage());
            return back()->with('error', 'Произошла ошибка при удалении сотрудника.');
        }
    }
}
