<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Admin\Models\TemplateSetting;
use Modules\Admin\Rules\DateOverlap;

class EmployeeSurveyTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $start_date = request('start_date');
        $end_date = request('end_date');
    

        return [
            'template_name' => 'bail|required|max:100|unique:employee_survey_templates,survey_name,' . $id . ',id,deleted_at,NULL',
            'sequence.*' => 'bail|required|distinct',
            'start_date' => 'bail|required|date',
            'end_date' => 'bail|required|date|after_or_equal:start_date|after:today',
            'question_text.*' => 'bail|required|max:500|',
            'answer_type.*' => 'bail|required|not_in:Choose one|',
            'customer_id'=>'bail|required',
            'role_id'=>'bail|required'
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
            'template_name.required' => 'Template name is required',
            'template_name.unique' => 'Template name already taken',
            'template_name.max' => 'Template name should not be more than 100 characters',
            'start_date.required' => 'Start Date is required',
            'start_date.date' => 'Start Date must be a date',
            'start_date.after_or_equal' => 'Start Date must be a date after or equal to today date',
            'end_date.required' => 'End Date is required',
            'end_date.date' => 'End Date must be a date',
            'end_date.after_or_equal' => 'End Date must be a date after or equal to start date',
            'sequence.*.required'=>'Sequence is required',
            'sequence.*.distinct'=>'Sequence should be distinct',
            'question_text.*.required' => 'Question text is required',
            'question_text.*.max' => 'Question text should not be more than 500 characters',
            'answer_type.*.required' => 'Answer type is required',
            'answer_type.*.not_in' => 'Answer type is required',
            'customer_id.required' => 'Please select atleast one customer',
            'role_id.required' => 'Please select atleast one role',
            
        ];
    }
}
