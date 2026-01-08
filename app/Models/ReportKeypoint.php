<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ReportKeypoint extends Model
{
	protected $connection = 'setfacts';
    protected $visible = ['id', 'keypoint','images'];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $fillable = [
        'report_id',
        'keypoint',
        'images',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    
    public function setFilenamesAttribute($value)
    {
        $this->attributes['images'] = json_encode($value);
    }
    public function report() {
        return $this->hasMany('App\Models\Report', 'keypoint');
    }

    /*public function report()
    {
        return $this->belongsTo('App\Models\Report');
    }*/
}
