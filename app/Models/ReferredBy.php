<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferredBy extends Model
{
    use SoftDeletes;

    protected $connection = 'setfacts';

     // Explicitly define the table name
     protected $table = 'referred_by';

    protected $fillable = [
        'website_id',
        'name',
        'mobile_number',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}
