<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkPhoto extends Model
{
    protected $fillable = [
        'work_record_id', 'photo_type', 'file_name', 'file_path',
        'original_name', 'mime_type', 'file_size', 'caption', 'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'decimal:2',
    ];

    public function workRecord(): BelongsTo
    {
        return $this->belongsTo(WorkRecord::class, 'work_record_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->photo_type) {
            'before' => '🟦 До работы',
            'during' => '🟨 Во время',
            'after' => '🟩 После',
            default => $this->photo_type,
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->photo_type) {
            'before' => 'bg-blue-100 text-blue-700',
            'during' => 'bg-amber-100 text-amber-700',
            'after' => 'bg-emerald-100 text-emerald-700',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    public function getFormattedSizeAttribute(): string
    {
        return $this->file_size . ' MB';
    }

    public function getViewUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}
