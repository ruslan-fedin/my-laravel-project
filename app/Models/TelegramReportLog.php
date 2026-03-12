<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramReportLog extends Model
{
    protected $fillable = [
        'timesheet_id',
        'date',
        'status_id',
        'employees_count',
        'message',
        'fields',
        'sent_by',
        'success',
        'error_message',
    ];

    protected $casts = [
        'fields' => 'array',
        'success' => 'boolean',
    ];

    public function timesheet(): BelongsTo
    {
        return $this->belongsTo(\App\Models\TravelTimesheet::class, 'timesheet_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Status::class, 'status_id');
    }
}
