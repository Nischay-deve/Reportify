<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocationState extends Model
{
    use SoftDeletes;
	
	protected $connection = 'setfacts';

    protected $visible = ['id', 'name','location_id'];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',        
    ];
    protected $fillable = [
        
        'name',
        'location_id',
        'created_at',
        'updated_at',
        
    ];
    

    public function location()
    {
        return $this->belongsTo('App\Models\Location');
    }
    
}
