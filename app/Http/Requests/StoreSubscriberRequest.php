<?php

namespace App\Http\Requests;

use App\Models\Subscriber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubscriberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */



    public function rules()
    {
        return [

            'name' => [
                'required','min:5','max:250',
            ],
            'email' => [
                'required','min:5','max:250','unique:subscribers',
            ],
            'place_id' => [
                'required',
            ],
            'mobile' => [
                'required',
            ],
            'occupation' => [
                'required','min:5','max:250',
            ],           
            'birthday' => [
                'required','date',
            ],
            'moreinfo' => [
                'required','min:5','max:500',
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
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'place_id.required' => 'Place is required',
            'mobile.required' => 'Mobile is required',
            'occupation.required' => 'Occupation is required',
            'birthday.required' => 'Birthday is required',
            'moreinfo.required' => 'More info is required',


            'name.min' => 'Name min length must be 5 character',
            'name.max' => 'Name max length must be 250 character',
            'email.min' => 'Email min length must be 5 character',
            'email.max' => 'Email max length must be 250 character',            
            'occupation.min' => 'Occupation min length must be 5 character',
            'occupation.max' => 'Occupation max length must be 250 character',          
            'moreinfo.min' => 'More info min length must be 5 character',
            'moreinfo.max' => 'More info max length must be 500 character',                        

        ];
    }     
}
