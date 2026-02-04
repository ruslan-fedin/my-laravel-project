<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TravelStatus extends Model {
    protected $fillable = ['name', 'short_name', 'color'];
}
