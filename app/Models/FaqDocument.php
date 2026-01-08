<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqDocument extends Model
{
    use HasFactory;
	protected $connection = 'setfacts';

    protected $visible = ['id', 'faq_id','document'];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
    protected $fillable = [
        'faq_id',
        'document',
        'created_at',
        'updated_at',
    ];

    public function faq() {
        return $this->belongsTo('App\Models\Faq');
    }
}
