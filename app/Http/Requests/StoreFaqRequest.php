<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFaqRequest extends FormRequest
{
    public function rules()
    {
        return [          
            'question' => [
                'required', 'min:3', 'max:250',
            ],
            'answer' => [
                'required', 'min:3', 'max:5000',
            ],
           
            'active' => [
                'required',
            ],
            'order' => [
                'required'
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

            'question.required' => 'Question is required',
            'question.min' => 'Question min length can be 5 character',
            'question.max' => 'Question max length can be 250 character',

            'answer.required' => 'Answer is required',
            'answer.min' => 'Answer min length can be 5 character',
            'answer.max' => 'Answer max length can be 5000 character',

            'active.required' => 'Active is required',
            'order.required' => 'Order is required',          
        ];
    }
}
