<?php

namespace App\Http\Controllers;

use App\Models\WorkRecord;
use App\Models\WorkType;
use App\Models\FlowerColor;
use App\Models\FlowerBed;
use App\Models\WorkRecordFlower;
use App\Models\WorkPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FlowerWorkController extends Controller
{
    /**
     * Список всех работ
     */
    public function index(Request $request)
    {
        $query = WorkRecord::with(['flowerBed', 'workType', 'createdBy', 'performedBy', 'flowers.flowerColor']);

        // 🔍 Поиск
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // 📅 Фильтр по году
        if ($request->has('year') && $request->year) {
            $query->whereYear('actual_start', $request->year)
                  ->orWhereYear('planned_start', $request->year);
        }

        // 🌸 Фильтр по клумбе
        if ($request->has('flower_bed_id') && $request->flower_bed_id) {
            $query->where('flower_bed_id', $request->flower_bed_id);
        }

        // 🔧 Фильтр по виду работы
        if ($request->has('work_type_id') && $request->work_type_id) {
            $query->where('work_type_id', $request->work_type_id);
        }

        // 📊 Фильтр по статусу
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $sortBy = $request->get('sort', 'actual_start');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $workRecords = $query->paginate(20);

        // Данные для фильтров
        $workTypes = WorkType::active()->ordered()->get();
        $flowerBeds = FlowerBed::where('is_active', true)->orderBy('short_name')->get();
        $years = WorkRecord::selectRaw('YEAR(actual_start) as year')
            ->whereNotNull('actual_start')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('flower-beds.works.index', compact('workRecords', 'workTypes', 'flowerBeds', 'years'));
    }

    /**
     * Форма создания работы
     */
    public function create()
    {
        $workTypes = WorkType::active()->ordered()->get();
        $flowerBeds = FlowerBed::where('is_active', true)->orderBy('short_name')->get();
        $flowerColors = FlowerColor::active()->ordered()->get();

        return view('flower-beds.works.create', compact('workTypes', 'flowerBeds', 'flowerColors'));
    }

    /**
     * Сохранение новой работы
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'flower_bed_id' => 'required|exists:flower_beds,id',
            'work_type_id' => 'required|exists:work_types,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planned,in_progress,completed,cancelled',
            'planned_start' => 'nullable|date',
            'planned_end' => 'nullable|date|after_or_equal:planned_start',
            'actual_start' => 'nullable|date',
            'actual_end' => 'nullable|date|after_or_equal:actual_start',
            'performed_by' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'progress' => 'nullable|integer|min:0|max:100',
            'flowers' => 'nullable|array',
        ]);

        $workRecord = WorkRecord::create([
            'flower_bed_id' => $validated['flower_bed_id'],
            'work_type_id' => $validated['work_type_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'planned_start' => $validated['planned_start'] ?? null,
            'planned_end' => $validated['planned_end'] ?? null,
            'actual_start' => $validated['actual_start'] ?? null,
            'actual_end' => $validated['actual_end'] ?? null,
            'performed_by' => $validated['performed_by'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'progress' => $validated['progress'] ?? 0,
            'created_by' => Auth::id(),
        ]);

        // Сохраняем цветы (если есть)
        if ($request->has('flowers') && is_array($request->flowers)) {
            foreach ($request->flowers as $flowerData) {
                if (!empty($flowerData['quantity'])) {
                    WorkRecordFlower::create([
                        'work_record_id' => $workRecord->id,
                        'flower_bed_id' => $workRecord->flower_bed_id,  // ✅ Наследуем из работы!
                        'flower_color_id' => $flowerData['flower_color_id'] ?? null,
                        'quantity' => (int) $flowerData['quantity'],
                        'flower_variety' => $flowerData['flower_variety'] ?? null,
                        'notes' => $flowerData['notes'] ?? null,
                        'created_by' => Auth::id(),
                    ]);
                }
            }
        }

        return redirect()->route('flower-beds.works.index')
            ->with('success', '✅ Работа создана');
    }

    /**
     * Просмотр работы
     */
    public function show(WorkRecord $workRecord)
    {
        $workRecord->load(['flowerBed', 'workType', 'createdBy', 'performedBy', 'flowers.flowerColor', 'photos']);

        $photosByType = $workRecord->photos->groupBy('photo_type');

        return view('flower-beds.works.show', compact('workRecord', 'photosByType'));
    }

    /**
     * Форма редактирования работы
     */
    public function edit(WorkRecord $workRecord)
    {
        $workRecord->load(['flowers.flowerColor']);

        $workTypes = WorkType::active()->ordered()->get();
        $flowerBeds = FlowerBed::where('is_active', true)->orderBy('short_name')->get();
        $flowerColors = FlowerColor::active()->ordered()->get();

        return view('flower-beds.works.edit', compact('workRecord', 'workTypes', 'flowerBeds', 'flowerColors'));
    }

    /**
     * Обновление работы
     */
    public function update(Request $request, WorkRecord $workRecord)
    {
        $validated = $request->validate([
            'flower_bed_id' => 'required|exists:flower_beds,id',
            'work_type_id' => 'required|exists:work_types,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planned,in_progress,completed,cancelled',
            'planned_start' => 'nullable|date',
            'planned_end' => 'nullable|date|after_or_equal:planned_start',
            'actual_start' => 'nullable|date',
            'actual_end' => 'nullable|date|after_or_equal:actual_start',
            'performed_by' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'progress' => 'nullable|integer|min:0|max:100',
            'flowers' => 'nullable|array',
        ]);

        $workRecord->update([
            'flower_bed_id' => $validated['flower_bed_id'],
            'work_type_id' => $validated['work_type_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'planned_start' => $validated['planned_start'] ?? null,
            'planned_end' => $validated['planned_end'] ?? null,
            'actual_start' => $validated['actual_start'] ?? null,
            'actual_end' => $validated['actual_end'] ?? null,
            'performed_by' => $validated['performed_by'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'progress' => $validated['progress'] ?? 0,
        ]);

        // Обновляем цветы
        $workRecord->flowers()->delete();

        if ($request->has('flowers') && is_array($request->flowers)) {
            foreach ($request->flowers as $flowerData) {
                if (!empty($flowerData['quantity'])) {
                    WorkRecordFlower::create([
                        'work_record_id' => $workRecord->id,
                        'flower_bed_id' => $workRecord->flower_bed_id,
                        'flower_color_id' => $flowerData['flower_color_id'] ?? null,
                        'quantity' => (int) $flowerData['quantity'],
                        'flower_variety' => $flowerData['flower_variety'] ?? null,
                        'notes' => $flowerData['notes'] ?? null,
                        'created_by' => Auth::id(),
                    ]);
                }
            }
        }

        return redirect()->route('flower-beds.works.index')
            ->with('success', '✅ Работа обновлена');
    }

    /**
     * Удаление работы
     */
    public function destroy(WorkRecord $workRecord)
    {
        foreach ($workRecord->photos as $photo) {
            Storage::disk('public')->delete($photo->file_path);
        }

        $workRecord->delete();

        return redirect()->route('flower-beds.works.index')
            ->with('success', '✅ Работа удалена');
    }

    /**
     * 🔥 AJAX: Загрузка фото
     */
    public function uploadPhoto(Request $request, WorkRecord $workRecord)
    {
        $validated = $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png,gif|max:10240',
            'photo_type' => 'required|in:before,during,after',
            'caption' => 'nullable|string|max:500',
            'taken_at' => 'nullable|date',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->store('work-photos/' . date('Y/m'), 'public');

        $photo = WorkPhoto::create([
            'work_record_id' => $workRecord->id,
            'photo_type' => $validated['photo_type'],
            'file_name' => $fileName,
            'file_path' => $filePath,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => round($file->getSize() / 1024 / 1024, 2),
            'caption' => $validated['caption'] ?? null,
            'taken_at' => $validated['taken_at'] ?? null,
            'uploaded_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => '✅ Фото загружено',
            'photo' => [
                'id' => $photo->id,
                'type' => $photo->photo_type,
                'type_label' => $photo->type_label,
                'type_color' => $photo->type_color,
                'caption' => $photo->caption,
                'view_url' => $photo->view_url,
                'formatted_size' => $photo->formatted_size,
            ]
        ]);
    }

    /**
     * 🔥 AJAX: Удаление фото
     */
    public function destroyPhoto(WorkPhoto $photo)
    {
        Storage::disk('public')->delete($photo->file_path);
        $photo->delete();

        return response()->json([
            'success' => true,
            'message' => '✅ Фото удалено'
        ]);
    }
}
