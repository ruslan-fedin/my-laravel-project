<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    use HasFactory;

    protected $fillable = ['start_date', 'end_date'];

    public function items()
    {
        return $this->hasMany(TimesheetItem::class);
    }


    public function employees()
    {
return $this->belongsToMany(Employee::class);    }
}
