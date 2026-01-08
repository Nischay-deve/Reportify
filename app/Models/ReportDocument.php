<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportDocument extends Model
{
    use HasFactory;
	
	protected $connection = 'setfacts';

    protected $visible = ['id', 'document','document_type','document_link','document_name'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $fillable = [
        'report_id',
        'document',
        'created_at',
        'updated_at',
        'deleted_at',
        'document_type',
        'document_link',
        'document_name'
    ];

    /**
     * Screenshot Type
     */
    const TYPE_FIR_COPY = 'fir_copy';
    const TYPE_GENERAL = 'general';

    public static $TYPE_ID_LABELS = [
        self::TYPE_FIR_COPY => 'FIR Copy',
        self::TYPE_GENERAL => 'General',
    ];

    public function report() {
        return $this->hasMany('App\Models\Report', 'document');
    }
}
