<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlowerColor extends Model
{
    protected $fillable = [
        'name', 'hex_code', 'code', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function workRecordFlowers(): HasMany
    {
        return $this->hasMany(WorkRecordFlower::class, 'flower_color_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getDisplayAttribute(): string
    {
        $icons = [
            'yellow' => '🟡', 'white' => '⚪', 'red' => '🔴', 'orange' => '🟠',
            'purple' => '🟣', 'pink' => '🩷', 'blue' => '🔵', 'mixed' => '🎨',
        ];
        $icon = $icons[$this->code] ?? '🎨';
        return "{$icon} {$this->name}";
    }
}
