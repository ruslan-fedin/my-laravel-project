<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PlantingPlanFlowerBed extends Pivot
{
    protected $table = 'planting_plan_flower_beds';

    protected $fillable = [
        'planting_plan_id', 'flower_bed_id', 'sort_order',
    ];

    public function plantingPlan()
    {
        return $this->belongsTo(PlantingPlan::class);
    }

    public function flowerBed()
    {
        return $this->belongsTo(FlowerBed::class);
    }
}
