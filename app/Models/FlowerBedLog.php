<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlowerBedLog extends Model
{
    /**
     * Поля, которые можно массово назначать
     */
    protected $fillable = [
        'flower_bed_id',
        'user_id',
        'action',
        'old_values',
        'new_values',
        'description',
        'is_editable',
    ];

    /**
     * Автоматическое приведение типов
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'is_editable' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Связь: Лог принадлежит клумбе
     */
    public function flowerBed(): BelongsTo
    {
        return $this->belongsTo(FlowerBed::class, 'flower_bed_id');
    }

    /**
     * Связь: Лог создан пользователем
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Атрибут: Читаемое название действия
     */
    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'created' => '🟢 Создана',
            'updated' => '🔵 Обновлена',
            'deleted' => '🔴 Удалена',
            'file_added' => '📎 Файл добавлен',
            'file_deleted' => '🗑️ Файл удалён',
            'log_edited' => '✏️ Лог изменён',
            default => $this->action,
        };
    }

    /**
     * Атрибут: Цвет для действия
     */
    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'created' => 'bg-emerald-100 text-emerald-700',
            'updated' => 'bg-blue-100 text-blue-700',
            'deleted' => 'bg-rose-100 text-rose-700',
            'file_added' => 'bg-indigo-100 text-indigo-700',
            'file_deleted' => 'bg-orange-100 text-orange-700',
            'log_edited' => 'bg-slate-100 text-slate-700',
            default => 'bg-slate-100 text-slate-700',
        };
    }
}
