<?php

namespace Modules\Hranalytics\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeWhistleblowerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

            // 'employee_id' => 'bail|required',
            'customer_id'=> 'bail|required',
            'whistleblower_subject' => 'bail|required|max:50',
            'whistleblower_category_id' => 'bail|required',
            'policy_id' => 'bail|required',
            'whistleblower_priority_id' => 'bail|required',
            'whistleblower_documentation' => 'bail|required|max:10000',
            'reg_manager_notes' =>  'bail|max:1000',
        ];
    }

    public function messages()
    {
        return [
            // 'employee_id.required' => 'Please choose the employee number.',
            'customer_id.required' => 'Please choose the customer.',
            'whistleblower_subject.required' => 'Please enter the subject.',
            'whistleblower_subject.max' => 'Please enter the subject within 50 characters.',
            'whistleblower_category_id.required' => 'Please choose the category.',
            'policy_id.required' => 'Please choose the policy violated',
            'whistleblower_priority_id.required' => 'Please choose the Priority.',
            'whistleblower_documentation.required' => 'Please enter the documentation.',
            'whistleblower_documentation.max' => 'Please enter the documentation within 10000 characters.',
            'reg_manager_notes.max' => 'Please enter the documentation within 1000 characters.',

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
