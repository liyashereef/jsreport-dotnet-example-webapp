<?php

namespace Modules\Hranalytics\Http\Requests;

class UserRatingRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $rules = [
            'employee_id' => 'bail|required',
            'subject' => 'bail|required|max:255',
            'employee_rating_lookup_id' => 'bail|required',
            'policy_id' => 'bail|required',
            'supporting_facts' => 'bail|required',
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
            'employee_id.required' => 'Please choose an employee',
            'subject.required' => 'Subject is required.',
            'subject.max' => 'Subject should not exceed 255 characters.',
            'employee_rating_lookup_id.required' => 'Rating is required.',
            'policy_id.required' => 'Policy is required',
            'supporting_facts.required' => 'Supporting facts is required.',
        ];
    }

}
