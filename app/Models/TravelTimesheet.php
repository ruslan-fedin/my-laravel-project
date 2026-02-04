<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TravelTimesheet extends Model
{
    use HasFactory;

    protected $fillable = ['start_date', 'end_date', 'title', 'status'];

    /**
     * Получаем сотрудников, которые уже есть в этом табеле через таблицу travel_items
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'travel_items', 'travel_timesheet_id', 'employee_id')
                    ->distinct();
    }
}
