<?php

namespace App\Http\Requests;

use App\Models\SmCalendar;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class MassDestroyIncidenceCalendarRequest extends FormRequest
{   
    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:incidence_calendars,id',
        ];
    }
}
