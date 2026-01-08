<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportScreenshot extends Model
{
    use HasFactory;
	protected $connection = 'setfacts';

    protected $visible = ['id', 'screenshot','screenshot_type'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $fillable = [
        'report_id',
        'screenshot',
        'created_at',
        'updated_at',
        'deleted_at',
        'screenshot_type',
    ];

    /**
     * Screenshot Type
     */
    const TYPE_FRONT_PAGE = 'front_page';
    const TYPE_FULL_NEWS = 'full_news';
    const TYPE_GENERAL = 'general';

    public static $TYPE_ID_LABELS = [
        self::TYPE_FRONT_PAGE => 'Front Page',
        self::TYPE_FULL_NEWS => 'Full News',
        self::TYPE_GENERAL => 'General',
    ];

    public function report() {
        return $this->hasMany('App\Models\Report', 'screenshot');
    }
}
