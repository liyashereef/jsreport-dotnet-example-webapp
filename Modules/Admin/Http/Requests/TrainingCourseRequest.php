<?php

namespace Modules\Admin\Http\Requests;

class TrainingCourseRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $rules = [
            'course_title' => "bail|required|max:250|unique:training_courses,course_title,{$id},id,deleted_at,NULL",
            'course_description' => "bail|required|max:1000",
            'course_objectives' => "bail|required|max:1000",
            'training_category_id' => "bail|required",
        ];
        if ($id == null) {
            $other_rules = [
                /*'course_file' => "bail|required_without:course_external_url", //mimetype:audio/mpeg,ppt,video/mp4
                'course_external_url' => "bail|required_without:course_file|max:255|nullable|url",*/
            ];
            $rules = array_merge($rules, $other_rules);
        }
        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'course_title.required' => 'Course title is required.',
            'course_title.unique' => 'This course title is already added.',
            'course_title.max' => 'The course title should not exceed 250 characters.',
            'course_description.required' => 'Course description is required.',
            'course_description.max' => 'The course title should not exceed 1000 characters.',
            'course_objectives.required' => 'Course objective is required.',
            'course_objectives.max' => 'The course title should not exceed 1000 characters.',
            'course_file.required_without' => 'Training course file is required.',
            'course_external_url.required_without' => 'URL is required.',
            'course_external_url.url' => 'Please enter valid URL',
            'training_category_id.required' => 'Please select category',

        ];
    }

}
