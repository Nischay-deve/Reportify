<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory;
	
	protected $connection = 'setfacts';

    use SoftDeletes;

    protected $visible = ['id', 'name','parent'];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',        
    ];
    protected $fillable = [        
        'name',
        'parent',
        'created_at',
        'updated_at',        
    ];
        
    const PARENT_INDIA = 'India';
    const PARENT_WORLD = 'World';

    public static $PARENT_LABELS = [
        self::PARENT_INDIA => 'India',
        self::PARENT_WORLD => 'World',
    ];


}
