<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * Список всех должностей с нумерацией
     */
    public function index()
    {
        $positions = Position::orderBy('name', 'asc')->paginate(10);
        return view('positions.index', compact('positions'));
    }

    /**
     * Форма создания новой должности (метод, который вызвал ошибку)
     */
    public function create()
    {
        return view('positions.create');
    }

    /**
     * Сохранение новой должности
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:positions,name',
        ]);

        Position::create([
            'name' => $request->name
        ]);

        return redirect()->route('positions.index')
                         ->with('success', 'ДОЛЖНОСТЬ УСПЕШНО ДОБАВЛЕНА');
    }

    /**
     * Форма редактирования
     */
    public function edit(Position $position)
    {
        return view('positions.edit', compact('position'));
    }

    /**
     * Обновление данных должности
     */
    public function update(Request $request, Position $position)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:positions,name,' . $position->id,
        ]);

        $position->update([
            'name' => $request->name
        ]);

        return redirect()->route('positions.index')
                         ->with('success', 'НАИМЕНОВАНИЕ ДОЛЖНОСТИ ОБНОВЛЕНО');
    }

    /**
     * Удаление должности
     */
    public function destroy(Position $position)
    {
        $position->delete();

        return redirect()->route('positions.index')
                         ->with('success', 'ДОЛЖНОСТЬ УДАЛЕНА');
    }
}
