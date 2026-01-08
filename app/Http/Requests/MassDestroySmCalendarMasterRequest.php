<?php

namespace App\Http\Requests;

use App\Models\SmCalendarMaster;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class MassDestroySmCalendarMasterRequest extends FormRequest
{   
    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:sm_calendar_masters,id',
        ];
    }
}
