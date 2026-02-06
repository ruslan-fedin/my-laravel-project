<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Substitution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrigadeController extends Controller
{



public function index()
{
    // 1. Загружаем структуру
    $masters = Employee::whereHas('position', function($q) {
        $q->where('name', 'like', '%Мастер%');
    })->with(['subordinates.subordinates', 'subordinates.position'])->get();

    // 2. СВОБОДНЫЕ РАБОЧИЕ:
    $freeWorkers = Employee::where(function($q) {
            // Ищем тех, у кого нет связи с руководителем
            $q->whereNull('parent_id')
              ->orWhere('parent_id', 0)
              ->orWhere('parent_id', '');
        })
        // ВАЖНО: Исключаем тех, кто сам является руководителем (Мастер/Бригадир)
        // Они не должны отображаться как "рабочие" для добавления
        ->whereHas('position', function($q) {
            $q->where('name', 'not like', '%Мастер%')
              ->where('name', 'not like', '%Бригадир%');
        })
        ->orderBy('last_name')
        ->get();

    // 3. Лидеры (для переноса и ВРИО)
    $allLeaders = Employee::whereHas('position', function($q) {
        $q->where('name', 'like', '%Мастер%')
          ->orWhere('name', 'like', '%Бригадир%');
    })->orderBy('last_name')->get();

    // 4. Бригадиры без мастеров
    $orphanBrigadiers = Employee::whereHas('position', function($q) {
            $q->where('name', 'like', '%Бригадир%');
        })->whereNull('parent_id')->get();

    return view('brigades.index', compact('masters', 'freeWorkers', 'allLeaders', 'orphanBrigadiers'));
}

    public function updateLocation(Request $request)
    {
        DB::table('brigade_locations')->updateOrInsert(
            ['brigadier_id' => $request->brigadier_id],
            ['location_name' => $request->location_name, 'updated_at' => now()]
        );
        return response()->json(['status' => 'success']);
    }

    public function updateLeader(Request $request)
    {
        $employee = Employee::findOrFail($request->employee_id);
        $employee->parent_id = $request->parent_id ?: null;
        $employee->save();
        return back()->with('success', 'Состав обновлен.');
    }

    public function startVacation(Request $request)
    {
        $brigadier = Employee::findOrFail($request->absentee_id);
        $brigadier->update([
            'status' => 'vacation',
            'substitute_id' => $request->substitute_id
        ]);
        return back()->with('success', 'Бригадир ушел в отпуск, люди переданы.');
    }

    public function returnVacation(Request $request)
    {
        $brigadier = Employee::findOrFail($request->brigadier_id);
        $brigadier->update(['status' => 'active', 'substitute_id' => null]);
        return back()->with('success', 'Бригадир вернулся в строй.');
    }





    public function startSubstitution(Request $request)
    {
        $request->validate([
            'absent_id' => 'required|exists:employees,id',
            'substitute_id' => 'required|exists:employees,id',
        ]);

        Substitution::create([
            'absent_id' => $request->absent_id,
            'substitute_id' => $request->substitute_id,
            'start_date' => now(),
            'is_active' => true
        ]);

        return back()->with('success', 'Замена активирована.');
    }

    public function endSubstitution($id)
    {
        Substitution::where('absent_id', $id)
            ->where('is_active', true)
            ->update(['is_active' => false, 'end_date' => now()]);

        return back()->with('success', 'Управление восстановлено.');
    }





}
