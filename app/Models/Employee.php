<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Employee extends Model
{
    use SoftDeletes;

    /**
     * Массив полей для массового заполнения.
     * Добавлены birth_date, hire_date и phone из вашей формы редактирования.
     */
    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'position_id',
        'parent_id',
        'status',
        'substitute_id',
        'is_active',
        'birth_date',
        'hire_date',
        'phone'
    ];

    /**
     * Приведение типов.
     * deleted_at автоматически обрабатывается трейтом SoftDeletes,
     * но мы добавляем его сюда для гарантии корректной работы с Carbon.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'birth_date' => 'date',
        'hire_date' => 'date',
        'deleted_at' => 'datetime',
    ];

    /**
     * Аксессор: Фамилия Имя Отчество полностью.
     * Всегда выводит ФИО целиком согласно вашему требованию.
     */
    public function getFullNameAttribute()
    {
        return trim("{$this->last_name} {$this->first_name} {$this->middle_name}");
    }


    // Внутри класса Employee
public function leader()
{
    return $this->belongsTo(Employee::class, 'parent_id');
}

    /**
     * Отношение к должности.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    /**
     * Отношение к подчиненным (для структуры бригад).
     */
    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'parent_id');
    }

    /**
     * Отношение к непосредственному руководителю.
     */
   

    /**
     * Отношение к тем, кого данный сотрудник замещает (ВРИО).
     */
    public function subbingFor(): HasMany
    {
        return $this->hasMany(Employee::class, 'substitute_id', 'id');
    }

    /**
     * Мутаторы для автоматического исправления регистра.
     * Сохраняют первую букву заглавной, остальные строчными.
     */
    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = Str::ucfirst(Str::lower(trim($value)));
    }

    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = Str::ucfirst(Str::lower(trim($value)));
    }

    public function setMiddleNameAttribute($value)
    {
        $this->attributes['middle_name'] = $value ? Str::ucfirst(Str::lower(trim($value))) : null;
    }
}
