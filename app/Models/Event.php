<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    // public $timestamps = false;
    // Specify the fillable fields
    protected $fillable = [
        'date',
        'type',
        'check_in',
        'check_out',
        'flight_number',
        'start_time',
        'end_time',
        'start_location',
        'end_location',
    ];
}
