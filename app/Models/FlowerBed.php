<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlowerBed extends Model
{
    use SoftDeletes;

    /**
     * Поля, которые можно массово назначать
     */
    protected $fillable = [
        'short_name',
        'full_name',
          'district',  // ✅ Добавляем
        'address',
        'area',
        'is_active',
        'is_perennial',  // ✅ Добавляем

        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * Автоматическое приведение типов
     */
    protected $casts = [
        'is_active' => 'boolean',
        'area' => 'decimal:2',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Связь: У клумбы много файлов
     */
    public function files(): HasMany
    {
        return $this->hasMany(FlowerBedFile::class, 'flower_bed_id');
    }

    /**
     * Связь: У клумбы много записей в логе
     */
    public function logs(): HasMany
    {
        return $this->hasMany(FlowerBedLog::class, 'flower_bed_id');
    }

    /**
     * Связь: Клумба создана пользователем
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Связь: Клумба обновлена пользователем
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope: Только активные клумбы
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Сортировка по названию
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('short_name');
    }
}
