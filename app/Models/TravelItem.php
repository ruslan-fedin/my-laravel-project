<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelItem extends Model
{
    // Указываем имя таблицы, если оно отличается от стандарта (множественного числа)
    protected $table = 'travel_items';

    protected $fillable = [
        'travel_timesheet_id',
        'employee_id',
        'date',
        'travel_status_id',
        'comment'
    ];

    // Связь с сотрудником
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Связь со статусом (Ц, В и т.д.)
    public function status()
    {
        return $this->belongsTo(TravelStatus::class, 'travel_status_id');
    }
}
