<?php

namespace Modules\Admin\Http\Requests;

use App\Rules\DistinctCaseInsensitive;

class IdsCustomQuestionRequest extends Request
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
            'question' => "bail|required|max:300",
            'answer_option.*' => ['bail', 'required', 'max:150',new DistinctCaseInsensitive($answer_option)],
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
            'question.required' => 'Question is required.',
            'answer_option.*.required' => 'Option is required.',
            'question.max'=>'Question should be less than 300 characters',
            'answer_option.*.max'=>'Option should be less than 150 characters',
            'answer_option.*.distinct'=>'This option already exists.'

        ];
    }
}
