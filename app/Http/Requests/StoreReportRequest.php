<?php

namespace App\Http\Requests;

use App\Models\Report;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportRequest extends FormRequest
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
                'required','min:5','max:250','unique:reports,heading,',
            ],
            'source' => [
                'required',
            ],
            'link' => [
                'required','unique:reports,link,', 
                #'url',
            ],
            'date' => [
                'required','date'
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
                'array'

            ],   
            'location_id' => [
                'required',
            ],
            'language_id' => [
                'required',
            ],            
            'front_page_screenshot' => [
                'required','mimes:jpeg,jpg,png,gif', //,'max:500' // max 500kb
            ],
            'full_news_screenshot' => [
                'required','mimes:jpeg,jpg,png,gif', //,'max:500' // max 500kb
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
            'module.required' => 'Category is required',
            'chapter_id.required' => 'Sub-Category is required',
            'heading.required' => 'Heading is required',
            'heading.min' => 'Heading min length can be 5 character',
            'heading.max' => 'Heading max length can be 250 character',
            'source.required' => 'Source is required',
            'link.required' => 'Link is required',
            'link.url' => 'News link must be a valid url',
            'date.required' => 'Publish Date is required',
            'tag.required' => 'Tag is required',
            'team_name.required' => 'Team is required',
            'team_user.required' => 'User is required',
            'keypoint.required' => 'Keypoints are required',
            'location_id.required' => 'Location is required',
            'front_page_screenshot.required' => 'Front page screenshot is required',
            'front_page_screenshot.mimes' => 'Front page screenshot can be either jpeg, jpg, gif or png',
            'front_page_screenshot.max' => 'Front page screenshot max size can be 500 kb',
            'full_news_screenshot.required' => 'Full news screenshot is required',
            'full_news_screenshot.mimes' => 'Full news screenshot can be either jpeg, jpg, gif or png',
            'full_news_screenshot.max' => 'Full news screenshot max size can be 500 kb',            
        ];
    }     
}
