<?php

namespace App\Http\Controllers;

use App\Models\WorkRecord;
use App\Models\WorkType;
use App\Models\WorkRecordFlower;
use App\Models\WorkPhoto;
use App\Models\FlowerBed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WorkRecordController extends Controller
{
    /**
     * Сохранение работы (AJAX)
     */
    public function store(Request $request, $flowerBedId)
    {
        $validated = $request->validate([
            'work_type_id' => 'required|exists:work_types,id',
            'work_date' => 'nullable|date',
            'flowers' => 'nullable|array',
        ]);

        $workRecord = WorkRecord::create([
            'flower_bed_id' => $flowerBedId,
            'work_type_id' => $validated['work_type_id'],
            'title' => 'Работа от ' . date('d.m.Y', strtotime($validated['work_date'] ?? now())),
            'description' => null,
            'status' => 'completed',
            'work_date' => $validated['work_date'] ?? now(),
            'notes' => null,
            'created_by' => Auth::id(),
        ]);

        if ($request->has('flowers') && is_array($request->flowers)) {
            foreach ($request->flowers as $flowerData) {
                if (!empty($flowerData['quantity'])) {
                    WorkRecordFlower::create([
                        'work_record_id' => $workRecord->id,
                        'quantity' => (int) $flowerData['quantity'],
                        'flower_color' => $flowerData['flower_color'] ?? null,
                        'flower_variety' => $flowerData['flower_variety'] ?? null,
                        'notes' => null,
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => '✅ Работа создана',
            'work' => [
                'id' => $workRecord->id,
                'date' => $workRecord->work_date->format('d.m.Y'),
                'type' => $workRecord->workType->name,
                'quantity' => $workRecord->total_quantity,
            ]
        ]);
    }

    /**
     * Загрузка фото (AJAX)
     */
    public function uploadPhoto(Request $request, WorkRecord $work)
    {
        $validated = $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png,gif|max:10240',
            'photo_type' => 'required|in:before,during,after',
            'caption' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->store('work-photos/' . date('Y/m'), 'public');

        $photo = WorkPhoto::create([
            'work_record_id' => $work->id,
            'photo_type' => $validated['photo_type'],
            'file_name' => $fileName,
            'file_path' => $filePath,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => round($file->getSize() / 1024 / 1024, 2),
            'caption' => $validated['caption'] ?? null,
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
     * Удаление фото (AJAX)
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

    /**
     * Удаление работы
     */
    public function destroy(WorkRecord $work)
    {
        foreach ($work->photos as $photo) {
            Storage::disk('public')->delete($photo->file_path);
        }

        $flowerBedId = $work->flower_bed_id;
        $work->delete();

        return redirect()->route('flower-beds.show', $flowerBedId)
            ->with('success', '✅ Работа удалена');
    }
}
