<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Http\Request;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */



    public function rules(Request $request)
    {
        // Notice the exists rule goes exists: table name, column, another column, value another column must equal
        return [
            // 'email' => ['required','email','exists:users,email,status,' . User::STATUS_APPROVED],

            'email' => ['required', 'string', 
             Rule::exists('users')->where(function ($query) use ($request) {
                return $query->where('email', $request->email)
                ->where('status', User::STATUS_APPROVED)
                ->WhereNotNull('email_verification');
                }),
            ],
            'password' => ['required','string'],
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
            'email.required' => 'Email is required',
            'password.required' => 'Password is required',
            'email.exists' => 'No user was found with this e-mail address or email is not yet approved'
        ];
    }     
}
