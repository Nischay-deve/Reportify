<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTime;

class Report extends Model {

    use SoftDeletes;
	
	protected $connection = 'setfacts';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $fillable = [
        'module_id',
        'chapter_id',
        'team_id',
        'user_id',
        'heading',
        'link',
        'team_name',
        'language',
        'publish_at',
        'created_at',
        'updated_at',
        'deleted_at',
        // 'screenshot',
        'feature_image_1',
        'feature_image_2',
        'location_id',
        'calendar_date',
        'first_time',
        'language_id',
        'report_source_id',
        'location_state_id',
        'calendar_date_description',
        'first_time_description',
        'active',
        'has_fir_document',
        'has_general_document',
        'has_featuredimage',
        'has_imagelink',
        'has_videolink',
        'has_followup',
        'link_clean',
        'link_hash',
        'website_id'
    ];

    protected $appends = ['publish_date_format'];

    public function getPublishDateFormatAttribute()
    {
        return Carbon::parse($this->publish_at)->format('F d, Y');
    }

    /**
     * Get the user that owns the comment.
     */
    public function tag() {
        return $this->hasMany('App\Models\ReportTag', 'report_id');
    }

    public function keypoint() {
        return $this->hasMany('App\Models\ReportKeypoint', 'report_id')->orderby('report_keypoints.id', 'ASC');
    }

    public function images() {
        return $this->hasMany('App\Models\ReportKeypoint', 'report_id');
    }

    public function videolink() {
        return $this->hasMany('App\Models\ReportVideolink', 'report_id');
    }

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function team()
    {
        return $this->hasOne('App\Models\Team','id','team_id');
    }

    public function chapter()
    {
        return $this->belongsTo('App\Models\Chapter');
    }
    public function report()
    {
        return $this->belongsTo('App\Models\ReportTag');
    }

    public function module()
    {
        return $this->belongsTo('App\Models\Module');
    }

    public function source()
    {
        return $this->belongsTo('App\Models\ReportSource');
    }    

    public function imagelink() {
        return $this->hasMany('App\Models\ReportImagelink', 'report_id');
    }

    public function screenshot() {
        return $this->hasMany('App\Models\ReportScreenshot', 'report_id')->orderby('screenshot_type');
    }

    public function feateredimage() {
        return $this->hasMany('App\Models\ReportFeaturedimage', 'report_id');
    }

    public function document() {
        return $this->hasMany('App\Models\ReportDocument', 'report_id');
    }    

    public function teamtag() {
        return $this->hasMany('App\Models\ReportTeamTag', 'report_id');
    }    

    public function followup() {
        return $this->hasMany('App\Models\ReportFollowup', 'report_id');
    }       

    public function nextReport($change_date) {
        if(!empty($change_date)){
            $created_from_date = $change_date;            
            $dateTime = DateTime::createFromFormat('d-m-Y', $created_from_date);
            $created_from_date_db = $dateTime->format('Y-m-d'). " 00:00:00";              
            $created_to_date_db = $dateTime->format('Y-m-d') . " 23:59:59";                        
        }else {        
            $created_from_date_db = $this->created_at->format('Y-m-d'). " 00:00:00";          
            $created_to_date_db = $this->created_at->format('Y-m-d'). " 23:59:59";            
        }

        $next_report = Report::where('id', '>', $this->id)
        ->whereBetween('created_at', [$created_from_date_db, $created_to_date_db])
        ->where('user_id', $this->user_id)
        ->orderBy('id')->first();

        return $next_report;
    }    


    public function previousReport($change_date) {
        
        if(!empty($change_date)){
            $created_from_date = $change_date;            
            $dateTime = DateTime::createFromFormat('d-m-Y', $created_from_date);
            $created_from_date_db = $dateTime->format('Y-m-d'). " 00:00:00";              
            $created_to_date_db = $dateTime->format('Y-m-d') . " 23:59:59";                        
        }else {        
            $created_from_date_db = $this->created_at->format('Y-m-d'). " 00:00:00";          
            $created_to_date_db = $this->created_at->format('Y-m-d'). " 23:59:59";            
        }           

        $previous_report = Report::where('id', '<', $this->id)
        ->whereBetween('created_at', [$created_from_date_db, $created_to_date_db])
        ->where('user_id', $this->user_id)
        ->orderBy('id','desc')->first();

        return $previous_report;
    }

    public function reportmedias() {
        return $this->hasMany('App\Models\ReportMedias', 'report_id','id');
    }  
    
    public function getBasePath()
    {
        return rtrim(config('app.upload_smcal_image'));
    }     
}
