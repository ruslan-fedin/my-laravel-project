<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    protected $fillable = ['name'];

    /**
     * Автоматическое форматирование: Первая заглавная, остальные строчные
     */
    public function setNameAttribute($value)
    {
        // trim - убирает пробелы, lower - всё в нижний регистр, ucfirst - первую букву в верхний
        $this->attributes['name'] = Str::ucfirst(Str::lower(trim($value)));
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'position_id');
    }
}
