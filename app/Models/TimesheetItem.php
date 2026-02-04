<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimesheetItem extends Model
{
    use HasFactory;

    // Эти поля разрешено заполнять массово (через updateOrCreate или create)
    protected $fillable = [
        'timesheet_id',
        'employee_id',
        'date',
        'status_id',
        'comment'
    ];

    // Связь с сотрудником
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Связь со статусом
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    // Связь с табелем
    public function timesheet()
    {
        return $this->belongsTo(Timesheet::class);
    }
}
