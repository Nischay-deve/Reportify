<?php

namespace App\Http\Requests;

use App\Models\Role;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class MassDestroyRoleRequest extends FormRequest
{
   

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:roles,id',
        ];
    }
}
