<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function index()
    {
        $statuses = Status::orderBy('name')->get();
        return view('statuses.index', compact('statuses'));
    }

    public function create()
    {
        return view('statuses.create');
    }

    public function store(Request $request)
{
    $data = $request->validate([
        'name'       => 'required|string|max:50|unique:statuses,name',
        'short_name' => 'required|string|max:3',
        'color'      => 'required|string|max:7',
    ]);

    Status::create($data);
    return redirect()->route('statuses.index')->with('success', 'СТАТУС СОЗДАН');
}

    public function edit(Status $status)
{
    return view('statuses.edit', compact('status'));
}

public function update(Request $request, Status $status)
{
    $data = $request->validate([
        'name'       => 'required|string|max:50|unique:statuses,name,' . $status->id,
        'short_name' => 'required|string|max:3',
        'color'      => 'required|string|max:7',
    ]);

    $status->update($data);

    return redirect()->route('statuses.index')->with('success', 'СТАТУС УСПЕШНО ОБНОВЛЕН');
}

    public function destroy(Status $status)
    {
        $status->delete();
        return redirect()->route('statuses.index')->with('success', 'СТАТУС УДАЛЕН');
    }
}
