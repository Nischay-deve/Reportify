<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamTag extends Model
{
    use SoftDeletes;

	protected $connection = 'setfacts';

    protected $visible = ['id', 'team_id','parent','tag'];
        
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $fillable = [
        'team_id',
        'parent',
        'tag',
        'created_at',
        'updated_at',
        'deleted_at',
    ];    
}
