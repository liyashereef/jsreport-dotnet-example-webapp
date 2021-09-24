<?php

namespace Modules\Admin\Http\Requests;

class OsgcCourseRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        return $rules = [
            'title' => "bail|required|max:255|unique:osgc_courses,title,{$id},id,deleted_at,NULL",
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'tile.required' => 'The Course Title is required.',
            'tile.unique' => 'This Course Title is already added.',
            'tile.max' => 'The Course Title should not exceed 255 characters.',
        ];
    }

}
