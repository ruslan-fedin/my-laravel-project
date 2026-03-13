<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkRecord extends Model
{
    protected $fillable = [
        'flower_bed_id', 'work_type_id', 'title', 'description',
        'status', 'work_date', 'created_by', 'notes',
    ];

    protected $casts = [
        'work_date' => 'date',
    ];

    public function flowerBed(): BelongsTo
    {
        return $this->belongsTo(FlowerBed::class, 'flower_bed_id');
    }

    public function workType(): BelongsTo
    {
        return $this->belongsTo(WorkType::class, 'work_type_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function flowers(): HasMany
    {
        return $this->hasMany(WorkRecordFlower::class, 'work_record_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(WorkPhoto::class, 'work_record_id');
    }

    public function getTotalQuantityAttribute(): int
    {
        return $this->flowers()->sum('quantity');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'completed' => '✅ Завершено',
            'in_progress' => '🔵 В работе',
            'planned' => '🟡 Запланировано',
            default => $this->status,
        };
    }
}
