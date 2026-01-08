<?php

namespace App\Http\Requests;

use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function rules()
    {
        return [          
            'title' => [
                'required', 'min:3', 'max:250',
            ],
            'description' => [
                'required', 'min:3', 'max:5000',
            ],
            // 'tags' => [
            //     'required', 'min:3', 'max:5000',
            // ],    
            'team_id' => [
                'required',
            ],
            'module_id' => [
                'required',
            ],
            'chapter_id' => [
                'required',
            ],                                            
            'date' => [
                'required', 'date'
            ],      
            'document' => [
                'required','mimes:jpeg,jpg,png,gif,pdf,docx, xlsx', //,'max:500' // max 500kb
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
            
            'title.required' => 'Title is required',
            'title.min' => 'Title min length can be 5 character',
            'title.max' => 'Title max length can be 250 character',

            'description.required' => 'Description is required',
            'description.min' => 'Description min length can be 5 character',
            'description.max' => 'Description max length can be 5000 character',         
            
            'tags.required' => 'Tags is required',

            'date.required' => 'Document Date is required',            

            'team_id.required' => 'Team is required',         
            'module_id.required' => 'Category is required',         
            'chapter_id.required' => 'Sub-Category is required',         

            'document.required' => 'document is required',       
            'document.mimes' => 'Document can only be in gif, png, jpg, jpeg, pdf, docx, xlsx format',       
        ];
    }
}
