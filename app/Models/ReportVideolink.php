<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ReportVideolink extends Model
{
    protected $connection = 'setfacts';
	
	protected $visible = ['id', 'videolink'];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $fillable = [
        'report_id',
        'videolink',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    
    public function report() {
        return $this->hasMany('App\Models\Report', 'videolink');
    }

    /*public function report()
    {
        return $this->belongsTo('App\Models\Report');
    }*/
}
