<?php

namespace Modules\Hranalytics\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeExitRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'project_name_id' => 'bail|required',
            'employee_name_id' => 'bail|required',
            'reason_id' => 'bail|required',
            'resignation_reason_id' => 'bail|required_if:reason_id,==' . ',' . RESIGNATION,
            'termination_reason_id' => 'bail|required_if:reason_id,==' . ',' . TERMINATION,
            'exit_interview_explantion' => 'bail|required|max:2000',
        ];
    }

    public function messages()
    {
        return [
            'project_name_id.required' => 'Please choose the project name.',
            'employee_name_id.required' => 'Please choose the employee name.',
            'reason_id.required' => 'Please choose the reason.',
            'resignation_reason_id.required_if' => 'Please choose the resignation reason.',
            'termination_reason_id.required_if' => 'Please choose the termination reason.',
            'exit_interview_explantion.required' => 'Please enter the exit interview explantion.',
            'exit_interview_explantion.max' => 'Explanation should not be greater than 2000 characters.',

        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
