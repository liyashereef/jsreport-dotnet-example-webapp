<?php

namespace Modules\LearningAndTraining\Http\Requests;
use Modules\LearningAndTraining\Rules\DistinctCaseInsensitive;

class TestQuestionsRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $answer_option = request('answer_option');
        return $rules = [
            'test_question' => "bail|required|max:255",
            'is_correct_answer' => "bail|required",
            'answer_option.*' => ['bail', 'required', 'max:150',new DistinctCaseInsensitive($answer_option)],
        ];
        // return $rules = [
        //     'test_question' => "bail|required|max:255",
        //     'answer_option.*'=>"bail|required|max:150|distinct, new RecommendedUniqueValidation($recommended_course)",
        //     'is_correct_answer'=>"bail|required"
        // ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'test_question.required' => 'Question is required.',
            'answer_option.*.required' => 'Option is required.',
            'test_question.max'=>'Question should be less than 255 characters',
            'answer_option.*.max'=>'Option should be less than 150 characters',
            'answer_option.*.distinct'=>'This option already exists.'

        ];
    }

}
