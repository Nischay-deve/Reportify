<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{
    use SoftDeletes;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'user_id',
        'team_id',
        'gender',
        'mobile',
        'dob',
        'slug',
        'created_at',
        'updated_at',
        'deleted_at',
        'place_id',
        'occupation',
        'moreinfo',
        'reference',
        'reference_mobile',
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
