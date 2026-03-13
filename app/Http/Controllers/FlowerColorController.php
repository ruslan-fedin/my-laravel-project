<?php

namespace App\Http\Controllers;

use App\Models\FlowerColor;
use Illuminate\Http\Request;

class FlowerColorController extends Controller
{
    public function index()
    {
        $colors = FlowerColor::orderBy('sort_order')->orderBy('name')->get();
        return view('flower-beds.colors.index', compact('colors'));
    }

    public function create()
    {
        return view('flower-beds.colors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:flower_colors,name',
            'hex_code' => 'required|string|max:7',
            'code' => 'required|string|max:100|unique:flower_colors,code',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        FlowerColor::create([
            'name' => $validated['name'],
            'hex_code' => $validated['hex_code'],
            'code' => $validated['code'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('flower-beds.colors.index')
            ->with('success', '✅ Цвет создан');
    }

    public function edit(FlowerColor $color)
    {
        return view('flower-beds.colors.edit', compact('color'));
    }

    public function update(Request $request, FlowerColor $color)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:flower_colors,name,' . $color->id,
            'hex_code' => 'required|string|max:7',
            'code' => 'required|string|max:100|unique:flower_colors,code,' . $color->id,
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $color->update([
            'name' => $validated['name'],
            'hex_code' => $validated['hex_code'],
            'code' => $validated['code'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('flower-beds.colors.index')
            ->with('success', '✅ Цвет обновлён');
    }

    public function destroy(FlowerColor $color)
    {
        if ($color->workRecordFlowers()->count() > 0) {
            return redirect()->route('flower-beds.colors.index')
                ->with('error', '❌ Нельзя удалить: есть связанные записи');
        }

        $color->delete();
        return redirect()->route('flower-beds.colors.index')
            ->with('success', '✅ Цвет удалён');
    }
}
