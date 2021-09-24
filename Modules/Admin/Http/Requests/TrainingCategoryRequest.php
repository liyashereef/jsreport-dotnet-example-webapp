<?php

namespace Modules\Admin\Http\Requests;

class TrainingCategoryRequest extends Request
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
            'course_category' => "bail|required|max:255|unique:training_categories,course_category,{$id},id,deleted_at,NULL",
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
            'course_category.required' => 'Course Category is required.',
            'course_category.unique' => 'This Course Category is already added.',
            'course_category.max' => 'The Course Category should not exceed 255 characters.',
        ];
    }

}
