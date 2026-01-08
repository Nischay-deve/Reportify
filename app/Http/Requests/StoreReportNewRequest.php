<?php

namespace App\Http\Requests;

use App\Models\Report;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportNewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */



    public function rules()
    {
        return [

            'heading' => [
                'required','min:5','max:250',
            ],
            'source' => [
                'required',
            ],
            'link' => [
                'required', 
                #'url',
            ],
            'date' => [
                'required','date'
            ],      
            'team_name' => [
                'required',
            ],
            'team_user' => [
                'required',
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
            'heading.required' => 'Heading is required',
            'heading.min' => 'Heading min length can be 5 character',
            'heading.max' => 'Heading max length can be 250 character',
            'source.required' => 'Source is required',
            'link.required' => 'Link is required',
            'link.url' => 'News link must be a valid url',
            'date.required' => 'Publish Date is required',       
            'team_name.required' => 'Team is required',
            'team_user.required' => 'User is required',                 
        ];
    }     
}
