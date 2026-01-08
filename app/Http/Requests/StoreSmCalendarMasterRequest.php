<?php

namespace App\Http\Requests;

use App\Models\SmCalendarMaster;
use Illuminate\Foundation\Http\FormRequest;

class StoreSmCalendarMasterRequest extends FormRequest
{
    public function rules()
    {
        return [          
            'title' => [
                'required', 'min:3', 'max:250',
            ],
            'description' => [
                'required', 'min:3', 'max:5000',
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
            
            'title.required' => 'Title is required',
            'title.min' => 'Title min length can be 5 character',
            'title.max' => 'Title max length can be 250 character',

            'description.required' => 'Description is required',
            'description.min' => 'Description min length can be 5 character',
            'description.max' => 'Description max length can be 5000 character',                     
        ];
    }
}
