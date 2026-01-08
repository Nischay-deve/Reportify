<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ReportTeamTag extends Model
{
	protected $connection = 'setfacts';
    protected $visible = ['id', 'report_id', 'team_id','tag_id'];
        
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $fillable = [
        'report_id',
        'team_id',
        'parent_id',
        'tag_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];    
}
