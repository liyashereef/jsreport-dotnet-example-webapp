<?php

namespace Modules\Admin\Http\Requests;

class TemplateQuestionsCategoryRequest extends Request
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
            'description' => 'bail|required|unique:template_questions_categories,description,' . $id . ',id,deleted_at,NULL',
            'average' => 'bail|required',
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
            'description.required' => 'Description is required',
            'description.unique' => 'Description has already been taken',
            'average.required' => 'Average is required',
        ];
    }

}
