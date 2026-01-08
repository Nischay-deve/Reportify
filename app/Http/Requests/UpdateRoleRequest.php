<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    

    public function rules()
    {
        return [
            'title'         => [
                'required',
            ],
            
        ];
    }
}
