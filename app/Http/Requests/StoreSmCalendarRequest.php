<?php

namespace App\Http\Requests;

use App\Models\SmCalendar;
use Illuminate\Foundation\Http\FormRequest;

class StoreSmCalendarRequest extends FormRequest
{
    public function rules()
    {
        $regex = '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';

        return [          
            // 'hashtag' => [
            //     'required', 'min:3', 'max:250',
            // ],
            'hashtag.*' => [
                'required', 'min:3', 'max:250',
            ],            
            // 'description' => [
            //     'required', 'min:3', 'max:5000',
            // ],
            'description.*' => [
                'required', 'min:3', 'max:5000',
            ],            
            // 'link' => [
            //     'required','regex:'.$regex,
            // ],
            'link.*' => [
                'required',
            ],            
            'date' => [
                'required', 'date'
            ],     
            'sm_calendar_master_id' => [
                'required'
            ],                  
            // 'image.*' => [
            //     'required','mimes:jpeg,jpg,png,gif', //,'max:500' // max 500kb
            // ],                
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
            
            'hashtag.required' => 'Hashtag is required',
            'hashtag.min' => 'Hashtag min length can be 5 character',
            'hashtag.max' => 'Hashtag max length can be 250 character',

            'description.required' => 'Description is required',
            'description.min' => 'Description min length can be 5 character',
            'description.max' => 'Description max length can be 5000 character',         
            
            'link.required' => 'Link is required',
            'link.regex' => 'Link must be a valid url',            

            'date.required' => 'Publish Date is required',            

            'image.required' => 'Image is required',       
            'image.mimes' => 'Image can only be in gif, png, jpg, jpeg format',       

            'sm_calendar_master_id.required' => 'Calendar master id is required',
        ];
    }
}
