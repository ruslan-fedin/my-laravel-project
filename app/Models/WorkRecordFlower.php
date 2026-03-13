<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkRecordFlower extends Model
{
    protected $fillable = [
        'work_record_id', 'quantity', 'flower_color', 'flower_variety', 'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function workRecord(): BelongsTo
    {
        return $this->belongsTo(WorkRecord::class, 'work_record_id');
    }
}
