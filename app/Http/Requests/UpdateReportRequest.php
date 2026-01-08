<?php

namespace App\Http\Requests;

use App\Models\Report;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    public function rules()
    {
        
        return [

            'module' => [
                'required',
            ],
            'chapter_id' => [
                'required',
            ],
            'heading' => [
                'required','unique:reports,heading,'.$this->report->id,
            ],

            'link' => [
                'required','unique:reports,link,'.$this->report->id,

            ],
            'date' => [
                'required',
            ],
            'source' => [
                'required',
            ],
            // 'tag' => [
            //     'required',
            // ],
            'team_name' => [
                'required',
            ],
            'team_user' => [
                'required',
            ],
            'keypoint' => [
                'required',
            ],
            'location_id' => [
                'required',
            ],
            'language_id' => [
                'required',
            ],            
        ];
    }
}
