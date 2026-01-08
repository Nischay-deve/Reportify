<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{    
    use SoftDeletes;
	protected $connection = 'setfacts';

    protected $dates = [
        'created_at',
        'updated_at', 
        'deleted_at',
    ];

    protected $fillable = [
        'date',
        'user_id',
        'team_id',
        'module_id',
        'chapter_id',
        'title',
        'description',
        'tags',
        'document',
        'is_deleted',
        'active',
        'created_at',
        'updated_at',
        'deleted_at',
        'website_id'
    ];


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

    public function module()
    {
        return $this->belongsTo('App\Models\Module');
    }    
}
