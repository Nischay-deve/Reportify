<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportMedias extends Model
{
    use HasFactory;

    protected $visible = ['id','report_id', 'media_type','media_file','media_link','media_name'];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
    protected $fillable = [
        'report_id',
        'media_type',
        'media_file',
        'media_link',
        'media_name',
        'created_at',
        'updated_at',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }      

    public function getBasePath()
    {
        return rtrim(config('app.upload_smcal_image'));
    }        
}
