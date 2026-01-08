<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    

    public function rules()
    {

        $id = $this->segment(3);
        
        return [
            'name' => [
                'required',
            ],
            'email' => [
                'required',
                'email',
                'max:255',

                Rule::unique('users')->ignore($this->user->id, 'id')->whereNull('deleted_at')
            ],

            'roles.*' => [
                'integer',
            ],
            'roles' => [
                'required',
            ],
        ];

    }
}
