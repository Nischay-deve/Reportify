<?php

namespace App\Http\Requests;

use App\Models\SmCalendar;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class MassDestroySmCalendarRequest extends FormRequest
{   
    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:sm_calendars,id',
        ];
    }
}
