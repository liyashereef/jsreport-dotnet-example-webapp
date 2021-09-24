<?php

namespace Modules\LearningAndTraining\Http\Requests;

class TestSettingsRequest extends Request
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
            'exam_name' => "bail|required|max:255|unique:test_course_masters,exam_name,{$id},id,deleted_at,NULL",
            'pass_percentage'=>"bail|required|numeric|between:0,100",
            'number_of_question'=>"bail|max:4"
            
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
            'exam_name.required' => 'Test Name is required.',
            'exam_name.unique' => 'This Test Name is already added.',
            'exam_name.max' => 'The Test Name should not exceed 255 characters.',
            'number_of_question.max' => 'The number of questions should be maximum 4 digit.',
        ];
    }

}
