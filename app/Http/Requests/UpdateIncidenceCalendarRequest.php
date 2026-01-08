<?php

namespace App\Http\Requests;

use App\Models\SmCalendar;
use Illuminate\Foundation\Http\FormRequest;

class UpdateIncidenceCalendarRequest extends FormRequest
{
    public function rules()
    {
        return [         
            'heading' => [
                'required', 'min:3', 'max:250',
            ],
            'description' => [
                'required', 'min:3', 'max:5000',
            ],            
            'date' => [
                'required', 'date'
            ],
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'user_id.required' => 'User is required',
            'report_id.required' => 'Report is required',

            'heading.required' => 'Heading is required',
            'heading.min' => 'Heading min length can be 5 character',
            'heading.max' => 'Heading max length can be 250 character',

            'description.required' => 'Description is required',
            'description.min' => 'Description min length can be 5 character',
            'description.max' => 'Description max length can be 5000 character',

            'date.required' => 'Publish Date is required',

        ];
    }
}
