<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{
    use HasFactory;
	
	protected $connection = 'setfacts';

    use SoftDeletes;

    protected $visible = ['id', 'name'];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',        
    ];
    protected $fillable = [        
        'name',
        'created_at',
        'updated_at',     
        'website_id'   
    ];

}
