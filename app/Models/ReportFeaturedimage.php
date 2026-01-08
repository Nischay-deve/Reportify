<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportFeaturedimage extends Model
{
    use HasFactory;
	
	protected $connection = 'setfacts';

    protected $visible = ['id', 'featured_image'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $fillable = [
        'report_id',
        'featured_image',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function report() {
        return $this->hasMany('App\Models\Report', 'featured_image');
    }
}
