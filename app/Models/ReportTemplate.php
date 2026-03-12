<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportTemplate extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'months',
        'format',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getPresets(): array
    {
        return [
            ['name' => '📅 Месячный', 'months' => 1],
            ['name' => '📊 Квартальный', 'months' => 3],
            ['name' => '📈 4 месяца', 'months' => 4],
            ['name' => '📊 Полугодовой', 'months' => 6],
            ['name' => '📅 Годовой', 'months' => 12],
        ];
    }
}
