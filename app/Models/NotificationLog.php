<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    protected $fillable = [
        'employee_id',
        'type',
        'channel',
        'recipient',
        'message',
        'sent',
        'sent_at',
        'error',
    ];

    protected $casts = [
        'sent' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function markAsSent(): void
    {
        $this->update([
            'sent' => true,
            'sent_at' => now(),
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'sent' => false,
            'error' => $error,
        ]);
    }
}
