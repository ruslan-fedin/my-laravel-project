<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlowerBedFile extends Model
{
    protected $fillable = [
        'flower_bed_id',
        'upload_session',
        'file_name',
        'file_path',
        'original_name',
        'file_type',
        'file_size',
        'notes',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function flowerBed(): BelongsTo
    {
        return $this->belongsTo(FlowerBed::class, 'flower_bed_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFormattedSizeAttribute(): string
    {
        if ($this->file_size) {
            return $this->file_size . ' MB';
        }
        return '—';
    }

    public function getFileIconAttribute(): string
    {
        return match($this->file_type) {
            'pdf' => 'fa-file-pdf',
            'image', 'photo' => 'fa-file-image',
            default => 'fa-file',
        };
    }

    public function getFileColorAttribute(): string
    {
        return match($this->file_type) {
            'pdf' => 'text-rose-500',
            'image', 'photo' => 'text-purple-500',
            default => 'text-slate-500',
        };
    }
}
