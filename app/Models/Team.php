<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use SoftDeletes;

	protected $connection = 'setfacts';

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
        'active',
        'website_id'
    ];
    

    public function report()
    {
        return $this->belongsTo('App\Models\Report');
    }

    public function chapter()
    {
        return $this->belongsTo('App\Models\chapter');
    }

    public function keypoint()
    {
        return $this->belongsTo('App\Models\ReportKeypoint');
    }

    public function module()
    {
        return $this->belongsTo('App\Models\Module');
    }


    
}
