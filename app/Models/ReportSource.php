<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportSource extends Model
{
    use SoftDeletes;
	protected $connection = 'setfacts';
    
    protected $visible = ['id', 'source'];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $fillable = [
        'source',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function report()
    {
        return $this->belongsTo('App\Models\Report');
    }    
        
}
