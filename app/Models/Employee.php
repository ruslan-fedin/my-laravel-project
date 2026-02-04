<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'last_name', 'first_name', 'middle_name', 'position_id',
        'birth_date', 'hire_date', 'phone', 'is_active'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'hire_date' => 'date',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Связь с должностью
     */
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    /**
     * Связь с записями в табеле (включая удаленных сотрудников)
     */
    public function timesheetItems()
    {
        return $this->hasMany(TimesheetItem::class, 'employee_id');
    }

    /**
     * Scope для получения только активных сотрудников
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->whereNull('deleted_at');
    }

    /**
     * Scope для получения удаленных сотрудников
     */
    public function scopeTrashedOnly($query)
    {
        return $query->whereNotNull('deleted_at');
    }

    /**
     * Проверка, удален ли сотрудник
     */
    public function getIsTrashedAttribute(): bool
    {
        return $this->trashed();
    }

    /**
     * Переопределение метода delete для мягкого удаления
     */
    public function delete()
    {
        if ($this->trashed()) {
            return parent::delete(); // Если уже удален, вызываем родительский
        }

        // Проверяем, есть ли связанные записи в табеле
        $hasTimesheetItems = $this->timesheetItems()->exists();

        // Можно добавить логирование
        if ($hasTimesheetItems) {
            \Log::info("Сотрудник {$this->full_name} перемещен в архив. Записей в табеле: " .
                      $this->timesheetItems()->count());
        }

        // Деактивируем сотрудника
        $this->is_active = false;
        $this->save();

        // Вызываем мягкое удаление
        return parent::delete();
    }

    /**
     * Восстановление сотрудника с активацией
     */
    public function restore()
    {
        $result = parent::restore();

        if ($result) {
            $this->is_active = true;
            $this->save();

            \Log::info("Сотрудник {$this->full_name} восстановлен из архива");
        }

        return $result;
    }

    /**
     * Полное удаление (только если нет записей в табеле)
     */
    public function forceDeleteIfSafe()
    {
        if ($this->timesheetItems()->exists()) {
            throw new \Exception('Невозможно удалить сотрудника, так как имеются связанные записи в табеле');
        }

        return $this->forceDelete();
    }

    /**
     * Форматирование Фамилии: Иванов
     */
    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = Str::ucfirst(Str::lower(trim($value)));
    }

    /**
     * Форматирование Имени: Иван
     */
    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = Str::ucfirst(Str::lower(trim($value)));
    }

    /**
     * Форматирование Отчества: Иванович
     */
    public function setMiddleNameAttribute($value)
    {
        $this->attributes['middle_name'] = $value ? Str::ucfirst(Str::lower(trim($value))) : null;
    }

    /**
     * Полное имя сотрудника
     */
    public function getFullNameAttribute()
    {
        return Str::ucfirst(Str::lower($this->last_name)) . ' ' .
               Str::ucfirst(Str::lower($this->first_name)) . ' ' .
               Str::ucfirst(Str::lower($this->middle_name));
    }

    /**
     * Краткое ФИО (Иванов И.И.)
     */
    public function getShortNameAttribute()
    {
        $lastName = Str::ucfirst(Str::lower($this->last_name));
        $firstName = $this->first_name ? mb_substr($this->first_name, 0, 1) . '.' : '';
        $middleName = $this->middle_name ? mb_substr($this->middle_name, 0, 1) . '.' : '';

        return $lastName . ' ' . $firstName . $middleName;
    }

    /**
     * Стаж работы
     */
    public function getExperienceAttribute()
    {
        if (!$this->hire_date) {
            return '—';
        }

        $hireDate = Carbon::parse($this->hire_date);
        $now = Carbon::now();

        $diff = $hireDate->diff($now);

        $years = $diff->y;
        $months = $diff->m;
        $days = $diff->d;

        $result = [];
        if ($years > 0) $result[] = $years . ' г.';
        if ($months > 0) $result[] = $months . ' мес.';
        if ($years == 0 && $months == 0) $result[] = $days . ' дн.';

        return implode(' ', $result);
    }
}
