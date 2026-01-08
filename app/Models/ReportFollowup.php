<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportFollowup extends Model
{
    use HasFactory;

	protected $connection = 'setfacts';
    protected $visible = ['id', 'report_id', 'followup_link','followup_name'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $fillable = [
        'report_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'followup_link',
        'followup_name'
    ];


    public function report() {
        return $this->hasMany('App\Models\Report', 'followup');
    }
}
