<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Substitution extends Model
{
    use HasFactory;

    protected $fillable = [
        'absent_id',
        'substitute_id',
        'start_date',
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Связь с тем, кто заменяет
    public function substitute()
    {
        return $this->belongsTo(Employee::class, 'substitute_id');
    }

    // Связь с тем, кто отсутствует
    public function absent()
    {
        return $this->belongsTo(Employee::class, 'absent_id');
    }
}
