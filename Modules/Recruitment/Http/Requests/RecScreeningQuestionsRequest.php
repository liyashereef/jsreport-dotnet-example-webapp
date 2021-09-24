<?php

namespace Modules\Recruitment\Http\Requests;

class RecScreeningQuestionsRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return $rules = [
            'answer.*' => 'bail|required|min:220',

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
            'answer.*.required' => 'Answer corresponding to this screening question is required',
            'answer.*.min' => 'Answer should be minimum 220 characters',
        ];
    }
}
