<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'notification_type',
        'enabled',
        'telegram_chat_id',
        'additional_settings',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'additional_settings' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getSetting(string $type, ?int $userId = null): ?self
    {
        return self::where('notification_type', $type)
            ->where(function($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhereNull('user_id');
            })
            ->first();
    }
}
