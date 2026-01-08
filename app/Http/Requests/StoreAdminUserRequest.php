<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdminUserRequest extends FormRequest
{
    
    public function rules()
    {
        return [
            'name'     => [
                'required',
            ],
            'email'    => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->whereNull('deleted_at')
            ],
            'password' => [
                'required',
            ]            
        ];
    }
}
