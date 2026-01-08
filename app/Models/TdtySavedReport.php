<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TdtySavedReport extends Model
{
    use HasFactory;

    protected $dates = [
        'created_at',
        'updated_at',
    ];
    protected $fillable = [
        'user_id',
        'name',
        'params',
        'website_id',
    ];    
}
