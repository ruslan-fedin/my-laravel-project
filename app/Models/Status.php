<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Status extends Model
{
    protected $fillable = ['name', 'short_name', 'color'];

    /**
     * Мутатор для имени статуса: Первая буква заглавная, остальные маленькие
     */
    public function setNameAttribute($value)
    {
        // trim убирает лишние пробелы
        // Str::lower делает всё маленьким
        // Str::ucfirst делает первую заглавной
        $this->attributes['name'] = Str::ucfirst(Str::lower(trim($value)));
    }

    /**
     * Короткий код (Я, О, Б) лучше оставить всегда заглавными для табеля
     */
    public function setShortNameAttribute($value)
    {
        $this->attributes['short_name'] = Str::upper(trim($value));
    }
}
