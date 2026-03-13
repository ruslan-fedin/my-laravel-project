<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlantingPlanBedColor extends Model
{
    protected $fillable = [
        'planting_plan_id', 'flower_bed_id', 'color_type', 'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public static $colorTypes = [
        'red' => ['name' => 'Красный', 'icon' => '🔴', 'hex' => '#EF4444'],
        'white' => ['name' => 'Белый', 'icon' => '⚪', 'hex' => '#F3F4F6'],
        'pink' => ['name' => 'Розовый', 'icon' => '🌸', 'hex' => '#EC4899'],
        'yellow' => ['name' => 'Жёлтый', 'icon' => '🟡', 'hex' => '#FCD34D'],
        'mix' => ['name' => 'Смесь', 'icon' => '🎨', 'hex' => '#6B7280'],
        'orange' => ['name' => 'Оранжевый', 'icon' => '🟠', 'hex' => '#F97316'],
        'purple' => ['name' => 'Фиолетовый', 'icon' => '🟣', 'hex' => '#A855F7'],
    ];

    public function plantingPlan(): BelongsTo
    {
        return $this->belongsTo(PlantingPlan::class, 'planting_plan_id');
    }

    public function flowerBed(): BelongsTo
    {
        return $this->belongsTo(FlowerBed::class, 'flower_bed_id');
    }

    public function getColorNameAttribute(): string
    {
        return self::$colorTypes[$this->color_type]['name'] ?? $this->color_type;
    }

    public function getColorIconAttribute(): string
    {
        return self::$colorTypes[$this->color_type]['icon'] ?? '🎨';
    }

    public function getColorHexAttribute(): string
    {
        return self::$colorTypes[$this->color_type]['hex'] ?? '#6B7280';
    }
}
