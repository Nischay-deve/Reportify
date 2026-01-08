<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
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
