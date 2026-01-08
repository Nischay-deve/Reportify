<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TdtyUser extends Authenticatable
{
    use SoftDeletes;

    protected $connection = 'setfacts';

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'mobile_number',
        'status',
        'email_verified',
        'email_verified_at',
        'access_level_id',
        'website_id',
        'referred_by_name',
        'referred_by_mobile_number',
        'referred_by_id',
        'verification_code',
        'is_global_user', // New field
    ];

    protected $casts = [
        'email_verified' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    // public function accessLevel()
    // {
    //     return $this->belongsTo(AccessLevel::class, 'access_level_id');
    // }

    public function referredBy()
    {
        return $this->belongsTo(ReferredBy::class, 'referred_by_id');
    }
}
