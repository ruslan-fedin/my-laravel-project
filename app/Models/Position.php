<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
}
