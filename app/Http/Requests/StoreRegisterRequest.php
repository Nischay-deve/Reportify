<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRegisterRequest extends FormRequest
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
            'place_id' => [
                'nullable',
            ],            
            'email' => [
                'required','min:5','max:250','unique:users',
            ],
            'password' => [
                'required','min:6','max:25','confirmed'
            ],            
            'mobile' => [
                'required','numeric','digits:10',
            ],
            'occupation' => [
                'nullable','min:5','max:250',
            ],           
            'birthday' => [
                'nullable','date',
            ],
            'moreinfo' => [
                'nullable','min:5','max:500',
            ],    
            'reference' => [
                'required','min:5','max:500',
            ],   
            'reference_mobile' => [
                'required','numeric','digits:10',
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
            'password.required' => 'Password is required',
            // 'place_id.required' => 'Place is required',            
            // 'occupation.required' => 'Occupation is required',
            'mobile.required' => 'Mobile is required',

            'reference.required' => 'Mobile is required',
            'reference_mobile.required' => 'Mobile is required',

            // 'birthday.required' => 'Birthday is required',
            // 'moreinfo.required' => 'More info is required',


            'name.min' => 'Name min length must be 5 character',
            'name.max' => 'Name max length must be 250 character',
            'email.min' => 'Email min length must be 5 character',
            'email.max' => 'Email max length must be 250 character',            
            'password.min' => 'Password min length must be 5 character',
            'password.max' => 'Password max length must be 25 character',                        
            'occupation.min' => 'Occupation min length must be 5 character',
            'occupation.max' => 'Occupation max length must be 250 character',          
            'moreinfo.min' => 'More info min length must be 5 character',
            'moreinfo.max' => 'More info max length must be 500 character',      
            
            'mobile.digits' => 'Mobile number must be of 10 digits',
            'reference_mobile.digits' => 'Mobile number must be of 10 digits',

        ];
    }     
}
