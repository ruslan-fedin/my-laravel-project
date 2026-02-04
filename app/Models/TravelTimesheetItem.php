<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelTimesheetItem extends Model
{
    // Указываем имя таблицы явно, чтобы Laravel не искал другое имя
    protected $table = 'travel_timesheet_items';

    protected $fillable = [
        'travel_timesheet_id',
        'employee_id',
        'date',
        'status_id',
        'comment'
    ];
}
