<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    /**
     * Notes
     */
    const NOTES_NARRATIVE_EVENT = 'Narrative event';
    const NOTES_GENERAL_EVENT = 'General event';
    const NOTES_NOT_IMPORTANT = 'Not important';
    const NOTES_PLEASE_DELETE = 'Please delete';

    public static $NOTES_LABELS = [
        self::NOTES_NARRATIVE_EVENT => 'Narrative event',
        self::NOTES_GENERAL_EVENT => 'General event',
        self::NOTES_NOT_IMPORTANT => 'Not important',
        self::NOTES_PLEASE_DELETE => 'Please delete',
    ];    
    
    protected $dates = [
        'created_at',
        'updated_at', 
        'deleted_at',        
    ];

    protected $fillable = [
        'user_id',
        'type',
        'website',
        'item_id',
        'notes',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }    
}
