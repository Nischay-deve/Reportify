<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{    
	protected $connection = 'setfacts';

    protected $dates = [
        'created_at',
        'updated_at', 
    ];

    protected $fillable = [
        'user_id',
        'question',
        'answer',
        'params',
        'active',
        'order',
        'created_at',
        'updated_at',
        'website_id',
        'response_date'
    ];

    public function documents() {
        return $this->hasMany('App\Models\FaqDocument');
    }

}
