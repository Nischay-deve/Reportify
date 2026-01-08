<?php

namespace App\Http\Requests;

use App\Models\Document;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class MassDestroyDocumentRequest extends FormRequest
{   
    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:documents,id',
        ];
    }
}
