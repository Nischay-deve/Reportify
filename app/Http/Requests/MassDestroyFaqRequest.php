<?php

namespace App\Http\Requests;

use App\Models\Faq;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class MassDestroyFaqRequest extends FormRequest
{   
    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:faqs,id',
        ];
    }
}
