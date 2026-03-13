<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlantingPlan extends Model
{
    protected $fillable = [
        'name', 'area', 'planting_rate', 'total_quantity', 'sort_order', 'created_by',
    ];

    protected $casts = [
        'area' => 'decimal:2',
        'planting_rate' => 'integer',
        'total_quantity' => 'integer',
        'sort_order' => 'integer',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function flowerBeds(): BelongsToMany
    {
        return $this->belongsToMany(FlowerBed::class, 'planting_plan_flower_beds')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function bedColors(): HasMany
    {
        return $this->hasMany(PlantingPlanBedColor::class, 'planting_plan_id');
    }

    public function getTotalColorsAttribute(): int
    {
        return $this->bedColors()->sum('quantity');
    }

    public function getColorQuantityForBed($bedId, $colorType): int
    {
        $color = $this->bedColors()
            ->where('flower_bed_id', $bedId)
            ->where('color_type', $colorType)
            ->first();
        return $color ? $color->quantity : 0;
    }
}
