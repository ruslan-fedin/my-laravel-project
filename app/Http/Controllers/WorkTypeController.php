<?php

namespace App\Http\Controllers;

use App\Models\WorkType;
use Illuminate\Http\Request;

class WorkTypeController extends Controller
{
    public function index()
    {
        $workTypes = WorkType::orderBy('sort_order')->orderBy('name')->get();
        return view('flower-beds.work-types.index', compact('workTypes'));
    }

    public function create()
    {
        return view('flower-beds.work-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:work_types,name',
            'code' => 'required|string|max:100|unique:work_types,code',
            'color' => 'required|string|max:7',
            'icon' => 'required|string|max:100',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        WorkType::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'color' => $validated['color'],
            'icon' => $validated['icon'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('flower-beds.work-types.index')
            ->with('success', '✅ Вид работы создан');
    }

    public function show(WorkType $workType)
    {
        $workType->load('workRecords');
        return view('flower-beds.work-types.show', compact('workType'));
    }

    public function edit(WorkType $workType)
    {
        return view('flower-beds.work-types.edit', compact('workType'));
    }

    public function update(Request $request, WorkType $workType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:work_types,name,' . $workType->id,
            'code' => 'required|string|max:100|unique:work_types,code,' . $workType->id,
            'color' => 'required|string|max:7',
            'icon' => 'required|string|max:100',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $workType->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'color' => $validated['color'],
            'icon' => $validated['icon'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('flower-beds.work-types.index')
            ->with('success', '✅ Вид работы обновлён');
    }

    public function destroy(WorkType $workType)
    {
        if ($workType->workRecords()->count() > 0) {
            return redirect()->route('flower-beds.work-types.index')
                ->with('error', '❌ Нельзя удалить: есть связанные работы');
        }

        $workType->delete();
        return redirect()->route('flower-beds.work-types.index')
            ->with('success', '✅ Вид работы удалён');
    }
}
