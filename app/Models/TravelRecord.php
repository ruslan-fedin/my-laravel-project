<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelRecord extends Model
{
    protected $fillable = ['travel_timesheet_id', 'employee_id', 'date', 'location_status', 'comment'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
