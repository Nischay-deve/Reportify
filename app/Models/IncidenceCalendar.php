<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidenceCalendar extends Model
{
    use HasFactory;

    protected $dates = [
        'created_at',
        'updated_at', 
    ];

    protected $fillable = [        
        'user_id',
        'report_id',
        'date',
        'heading',
        'description',
        'active',
        'created_at',
        'updated_at',
    ];

}
