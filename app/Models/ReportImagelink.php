<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportImagelink extends Model
{
    use HasFactory;
	protected $connection = 'setfacts';

    protected $visible = ['id', 'imagelink'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $fillable = [
        'report_id',
        'imagelink',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function report() {
        return $this->hasMany('App\Models\Report', 'imagelink');
    }
}
