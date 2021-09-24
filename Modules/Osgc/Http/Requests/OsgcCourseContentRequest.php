<?php

namespace Modules\Osgc\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OsgcCourseContentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $rules = [
            //'name' => "bail|required|max:27|unique:osgc_course_content_sections,name,{$id},id,deleted_at,NULL",
            'name' => 'bail|required|max:35|unique:osgc_course_content_sections,name,' . request('id') . ',id,header_id,' . request('header_id'),
            'sort_order' => 'bail|required|unique:osgc_course_content_sections,sort_order,' . request('id') . ',id,header_id,' . request('header_id'),
           // 'course_file_name' => "required",
            'content_type_id' => "required",
            'header_id' => "required",
            'content_status' => "required",
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function messages()
    {
        return [
            'name.required' => 'The Section Name is required.',
            'name.unique' => 'This Section Name is already added.',
            'name.required' => 'The Section Name is required.',
            'header_id.required' => 'The Heading is required.',
            'sort_order.required' => 'The Sort Order is required.',
            'price.required' => 'The Price is required.',
            'content_type_id.required' => 'The Content Type is required.',
            'content_status.required' => 'The Content Status is required.',
            
        ];
    }
    public function authorize()
    {
        return true;
    }
}
