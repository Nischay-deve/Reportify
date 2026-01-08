<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ReportTag extends Model
{
	protected $connection = 'setfacts';
    protected $visible = ['id', 'tag'];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $fillable = [
        'report_id',
        'tag',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    
    public function report() {
        return $this->hasMany('App\Models\Report', 'tag');
    }

    /*public function report()
    {
        return $this->belongsTo('App\Models\Report');
    }*/
}
