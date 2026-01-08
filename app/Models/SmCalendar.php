<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmCalendar extends Model
{    
    protected $connection = 'setfacts';
    
    protected $dates = [
        'created_at',
        'updated_at', 
    ];

    protected $fillable = [
        'date',
        'sm_calendar_master_id',
        'user_id',
        'hashtag',
        'description',
        'link',
        'active',
        'created_at',
        'updated_at',
        'image',
    ];

    public function smcalendarmaster()
    {
        return $this->belongsTo(SmCalendarMaster::class);
    }    
}
