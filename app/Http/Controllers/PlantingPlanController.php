<?php

namespace App\Http\Controllers;

use App\Models\PlantingPlan;
use App\Models\PlantingPlanBedColor;
use App\Models\FlowerBed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlantingPlanController extends Controller
{
    public function index()
    {
        $plans = PlantingPlan::with(['flowerBeds', 'bedColors', 'createdBy'])
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        $totals = [
            'area' => $plans->sum('area'),
            'quantity' => $plans->sum('total_quantity'),
            'colors' => [],
        ];

        foreach (PlantingPlanBedColor::$colorTypes as $type => $data) {
            $totals['colors'][$type] = $plans->sum(function($plan) use ($type) {
                return $plan->bedColors()->where('color_type', $type)->sum('quantity');
            });
        }

        $defaultRate = 60;

        return view('planting-plans.index', compact('plans', 'totals', 'defaultRate'));
    }

    public function create()
    {
        $flowerBeds = FlowerBed::where('is_active', true)
            ->orderBy('district')
            ->orderBy('full_name')
            ->get();

        $colorTypes = PlantingPlanBedColor::$colorTypes;

        return view('planting-plans.create', compact('flowerBeds', 'colorTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'flower_beds' => 'required|array|min:1',
            'planting_rate' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $totalArea = 0;
            $totalQuantity = 0;

            foreach ($validated['flower_beds'] as $bedData) {
                $totalArea += floatval($bedData['area'] ?? 0);
                $totalQuantity += intval($bedData['total'] ?? 0);
            }

            $plan = PlantingPlan::create([
                'name' => $validated['name'],
                'area' => $totalArea,
                'planting_rate' => $validated['planting_rate'],
                'total_quantity' => $totalQuantity,
                'sort_order' => PlantingPlan::max('sort_order') + 1,
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['flower_beds'] as $bedData) {
                if (!empty($bedData['flower_bed_id'])) {
                    $plan->flowerBeds()->attach($bedData['flower_bed_id'], [
                        'sort_order' => $bedData['sort_order'] ?? 0,
                    ]);

                    if (!empty($bedData['colors'])) {
                        foreach ($bedData['colors'] as $colorType => $quantity) {
                            if ($quantity > 0) {
                                PlantingPlanBedColor::create([
                                    'planting_plan_id' => $plan->id,
                                    'flower_bed_id' => $bedData['flower_bed_id'],
                                    'color_type' => $colorType,
                                    'quantity' => (int) $quantity,
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('planting-plans.index')
                ->with('success', '✅ План посадок создан');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', '❌ Ошибка при создании: ' . $e->getMessage());
        }
    }

    public function edit(PlantingPlan $plan)
    {
        $plan->load(['flowerBeds', 'bedColors']);

        $flowerBeds = FlowerBed::where('is_active', true)
            ->orderBy('district')
            ->orderBy('full_name')
            ->get();

        $colorTypes = PlantingPlanBedColor::$colorTypes;

        $bedColors = [];
        foreach ($plan->bedColors as $color) {
            if (!isset($bedColors[$color->flower_bed_id])) {
                $bedColors[$color->flower_bed_id] = [];
            }
            $bedColors[$color->flower_bed_id][$color->color_type] = $color->quantity;
        }

        return view('planting-plans.edit', compact('plan', 'flowerBeds', 'colorTypes', 'bedColors'));
    }

    public function update(Request $request, PlantingPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'flower_beds' => 'required|array|min:1',
            'planting_rate' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $totalArea = 0;
            $totalQuantity = 0;

            foreach ($validated['flower_beds'] as $bedData) {
                $totalArea += floatval($bedData['area'] ?? 0);
                $totalQuantity += intval($bedData['total'] ?? 0);
            }

            $plan->update([
                'name' => $validated['name'],
                'area' => $totalArea,
                'planting_rate' => $validated['planting_rate'],
                'total_quantity' => $totalQuantity,
            ]);

            $plan->flowerBeds()->detach();
            $plan->bedColors()->delete();

            foreach ($validated['flower_beds'] as $bedData) {
                if (!empty($bedData['flower_bed_id'])) {
                    $plan->flowerBeds()->attach($bedData['flower_bed_id'], [
                        'sort_order' => $bedData['sort_order'] ?? 0,
                    ]);

                    if (!empty($bedData['colors'])) {
                        foreach ($bedData['colors'] as $colorType => $quantity) {
                            if ($quantity > 0) {
                                PlantingPlanBedColor::create([
                                    'planting_plan_id' => $plan->id,
                                    'flower_bed_id' => $bedData['flower_bed_id'],
                                    'color_type' => $colorType,
                                    'quantity' => (int) $quantity,
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('planting-plans.index')
                ->with('success', '✅ План посадок обновлён');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', '❌ Ошибка при обновлении: ' . $e->getMessage());
        }
    }

    public function destroy(PlantingPlan $plan)
    {
        $plan->delete();

        return redirect()->route('planting-plans.index')
            ->with('success', '✅ План посадок удалён');
    }

    public function updateRate(Request $request)
    {
        $validated = $request->validate([
            'planting_rate' => 'required|integer|min:1',
        ]);

        $plans = PlantingPlan::all();
        foreach ($plans as $plan) {
            $plan->update([
                'planting_rate' => $validated['planting_rate'],
                'total_quantity' => $plan->area * $validated['planting_rate'],
            ]);
        }

        return redirect()->route('planting-plans.index')
            ->with('success', '✅ Норма посадки обновлена');
    }
}
