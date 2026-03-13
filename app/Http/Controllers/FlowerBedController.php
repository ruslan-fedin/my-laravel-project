<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FlowerBed;
use App\Models\FlowerBedFile;
use App\Models\FlowerBedLog;
use App\Models\WorkRecord;
use App\Models\WorkType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FlowerBedController extends Controller
{
    /**
     * Список всех клумб
     */
    public function index(Request $request)
    {
        $query = FlowerBed::with(['createdBy', 'updatedBy', 'files']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('short_name', 'LIKE', "%{$search}%")
                  ->orWhere('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('district', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        if ($request->has('perennial') && in_array($request->perennial, ['0', '1'])) {
            $query->where('is_perennial', $request->perennial === '1');
        }

        if ($request->has('status') && in_array($request->status, ['0', '1'])) {
            $query->where('is_active', $request->status === '1');
        }

        $sortBy = $request->get('sort', 'short_name');
        $sortOrder = $request->get('order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $flowerBeds = $query->paginate(20);

        $districts = FlowerBed::select('district')
            ->whereNotNull('district')
            ->distinct()
            ->orderBy('district')
            ->pluck('district');

        return view('flower-beds.index', compact('flowerBeds', 'districts'));
    }

    public function create()
    {
        return view('flower-beds.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'short_name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'district' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'area' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'is_perennial' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $flowerBed = FlowerBed::create([
            'short_name' => $validated['short_name'],
            'full_name' => $validated['full_name'],
            'district' => $validated['district'] ?? null,
            'address' => $validated['address'] ?? null,
            'area' => $validated['area'],
            'is_active' => $validated['is_active'] ?? true,
            'is_perennial' => $validated['is_perennial'] ?? false,
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        FlowerBedLog::create([
            'flower_bed_id' => $flowerBed->id,
            'user_id' => Auth::id(),
            'action' => 'created',
            'new_values' => [
                'short_name' => $flowerBed->short_name,
                'full_name' => $flowerBed->full_name,
                'district' => $flowerBed->district,
                'area' => $flowerBed->area,
                'is_perennial' => $flowerBed->is_perennial,
            ],
            'description' => 'Клумба создана',
            'is_editable' => true,
        ]);

        return redirect()->route('flower-beds.index')
            ->with('success', '✅ Клумба создана');
    }

    /**
     * Просмотр клумбы (ИСПРАВЛЕНО: добавлена загрузка photos и flowers)
     */
    public function show(FlowerBed $flowerBed)
    {
        $flowerBed->load(['logs', 'files']);

        // Загружаем историю работ с фото и цветами
        $workRecords = WorkRecord::with(['workType', 'flowers', 'photos', 'createdBy'])
            ->where('flower_bed_id', $flowerBed->id)
            ->orderByDesc('work_date')
            ->get();

        $workTypes = WorkType::active()->ordered()->get();

        return view('flower-beds.show', compact('flowerBed', 'workRecords', 'workTypes'));
    }

    public function edit(FlowerBed $flowerBed)
    {
        $flowerBed->load('files');
        return view('flower-beds.edit', compact('flowerBed'));
    }

    public function update(Request $request, FlowerBed $flowerBed)
    {
        $validated = $request->validate([
            'short_name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'district' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'area' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'is_perennial' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $oldValues = $flowerBed->only(['short_name', 'full_name', 'district', 'address', 'area', 'is_active', 'is_perennial', 'notes']);

        $flowerBed->update([
            'short_name' => $validated['short_name'],
            'full_name' => $validated['full_name'],
            'district' => $validated['district'] ?? null,
            'address' => $validated['address'] ?? null,
            'area' => $validated['area'],
            'is_active' => $validated['is_active'] ?? true,
            'is_perennial' => $validated['is_perennial'] ?? false,
            'notes' => $validated['notes'] ?? null,
            'updated_by' => Auth::id(),
        ]);

        $newValues = $flowerBed->only(['short_name', 'full_name', 'district', 'address', 'area', 'is_active', 'is_perennial', 'notes']);

        FlowerBedLog::create([
            'flower_bed_id' => $flowerBed->id,
            'user_id' => Auth::id(),
            'action' => 'updated',
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => 'Клумба обновлена',
            'is_editable' => true,
        ]);

        return redirect()->route('flower-beds.index')
            ->with('success', '✅ Клумба обновлена');
    }

    public function destroy(FlowerBed $flowerBed)
    {
        FlowerBedLog::create([
            'flower_bed_id' => $flowerBed->id,
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'old_values' => $flowerBed->toArray(),
            'description' => 'Клумба удалена',
            'is_editable' => false,
        ]);

        $flowerBed->delete();
        return redirect()->route('flower-beds.index')
            ->with('success', '✅ Клумба удалена');
    }

    public function uploadFile(Request $request, FlowerBed $flowerBed)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,gif|max:10240',
            'file_notes' => 'nullable|array',
            'file_notes.*' => 'nullable|string|max:500',
            'file_index' => 'nullable|integer',
        ]);

        $currentCount = $flowerBed->files()->count();
        if ($currentCount >= 10) {
            return response()->json([
                'success' => false,
                'message' => '❌ Достигнут лимит файлов (10)'
            ], 400);
        }

        $file = $request->file('file');
        $mimeType = $file->getMimeType();
        $fileType = match(true) {
            str_contains($mimeType, 'pdf') => 'pdf',
            str_contains($mimeType, 'image') => 'image',
            default => 'photo',
        };

        $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->store('flower-beds', 'public');

        $fileNotes = $validated['file_notes'] ?? [];
        $fileIndex = $validated['file_index'] ?? 0;
        $note = $fileNotes[$fileIndex] ?? null;

        $flowerBedFile = FlowerBedFile::create([
            'flower_bed_id' => $flowerBed->id,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'original_name' => $file->getClientOriginalName(),
            'file_type' => $fileType,
            'file_size' => round($file->getSize() / 1024 / 1024, 2),
            'notes' => $note,
            'uploaded_by' => Auth::id(),
        ]);

        FlowerBedLog::create([
            'flower_bed_id' => $flowerBed->id,
            'user_id' => Auth::id(),
            'action' => 'file_added',
            'new_values' => [
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $fileType,
            ],
            'description' => 'Файл добавлен: ' . $file->getClientOriginalName(),
            'is_editable' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => '✅ Файл загружен',
            'file' => [
                'id' => $flowerBedFile->id,
                'name' => $flowerBedFile->original_name,
                'size' => $flowerBedFile->formatted_size,
                'type' => $flowerBedFile->file_type,
                'icon' => $flowerBedFile->file_icon,
                'color' => $flowerBedFile->file_color,
                'notes' => $flowerBedFile->notes,
            ]
        ]);
    }

    public function destroyFile(FlowerBedFile $file)
    {
        $flowerBed = $file->flowerBed;

        Storage::disk('public')->delete($file->file_path);

        FlowerBedLog::create([
            'flower_bed_id' => $flowerBed->id,
            'user_id' => Auth::id(),
            'action' => 'file_deleted',
            'old_values' => [
                'file_name' => $file->file_name,
                'file_type' => $file->file_type,
            ],
            'description' => 'Файл удалён: ' . $file->original_name,
            'is_editable' => false,
        ]);

        $file->delete();

        return response()->json([
            'success' => true,
            'message' => '✅ Файл удалён'
        ]);
    }

    public function downloadFile(FlowerBedFile $file)
    {
        return Storage::disk('public')->download($file->file_path, $file->original_name);
    }

    public function viewFile(FlowerBedFile $file)
    {
        $path = Storage::disk('public')->path($file->file_path);
        return response()->file($path);
    }

    public function editFlowerBedLog(FlowerBedLog $log)
    {
        return response()->json([
            'success' => true,
            'log' => [
                'id' => $log->id,
                'description' => $log->description,
                'is_editable' => $log->is_editable,
            ]
        ]);
    }

    public function updateFlowerBedLog(Request $request, FlowerBedLog $log)
    {
        if (!$log->is_editable) {
            return response()->json([
                'success' => false,
                'message' => '❌ Эта запись лога не может быть изменена'
            ], 403);
        }

        $validated = $request->validate([
            'description' => 'required|string|max:500',
        ]);

        $oldDescription = $log->description;

        $log->update([
            'description' => $validated['description'],
        ]);

        FlowerBedLog::create([
            'flower_bed_id' => $log->flower_bed_id,
            'user_id' => Auth::id(),
            'action' => 'log_edited',
            'old_values' => ['description' => $oldDescription],
            'new_values' => ['description' => $validated['description']],
            'description' => 'Запись лога изменена',
            'is_editable' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => '✅ Лог обновлён',
            'new_description' => $validated['description'],
        ]);
    }

    public function deleteFlowerBedLog(FlowerBedLog $log)
    {
        $flowerBedId = $log->flower_bed_id;

        FlowerBedLog::create([
            'flower_bed_id' => $flowerBedId,
            'user_id' => Auth::id(),
            'action' => 'log_deleted',
            'old_values' => [
                'log_id' => $log->id,
                'description' => $log->description,
                'action' => $log->action,
            ],
            'description' => 'Запись лога удалена',
            'is_editable' => false,
        ]);

        $log->delete();

        return response()->json([
            'success' => true,
            'message' => '✅ Лог удалён'
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('query', '');

        if (empty($query)) {
            return response()->json(['results' => []]);
        }

        $results = FlowerBed::with(['files'])
            ->where(function($q) use ($query) {
                $q->where('short_name', 'LIKE', "%{$query}%")
                  ->orWhere('full_name', 'LIKE', "%{$query}%")
                  ->orWhere('district', 'LIKE', "%{$query}%")
                  ->orWhere('address', 'LIKE', "%{$query}%")
                  ->orWhere('notes', 'LIKE', "%{$query}%");
            })
            ->orderBy('short_name')
            ->limit(20)
            ->get()
            ->map(function($bed) {
                return [
                    'id' => $bed->id,
                    'short_name' => $bed->short_name,
                    'full_name' => $bed->full_name,
                    'district' => $bed->district,
                    'address' => $bed->address,
                    'area' => number_format($bed->area, 2),
                    'is_active' => $bed->is_active,
                    'is_perennial' => $bed->is_perennial,
                    'files_count' => $bed->files->count(),
                    'url_show' => route('flower-beds.show', $bed->id),
                    'url_edit' => route('flower-beds.edit', $bed->id),
                ];
            });

        return response()->json(['results' => $results]);
    }
}
