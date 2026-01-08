<?php

namespace App\Http\Requests;

use App\Models\ReportSource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportSourceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */



    public function rules()
    {
        return [
            'source' => [
                'required','min:2','max:250','unique:report_sources,source,',
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
            'source.required' => 'News source is required',
            'source.min' => 'News source min length can be 2 character',
            'source.max' => 'News source max length can be 250 character',            
            'source.unique' => 'News source already exists',            
        ];
    }     
}
