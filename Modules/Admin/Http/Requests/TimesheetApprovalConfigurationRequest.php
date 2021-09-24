<?php

namespace Modules\Admin\Http\Requests;

class TimesheetApprovalConfigurationRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $email_1_time = request('email_1_time');
        $email_2_time = request('email_2_time');
        $early_0 = request('early_0');
        $early_1 = request('early_1');
        $early_2 = request('early_2');
        $row_ids = [0,1,2,3,4];
        $rules = [
            'day' => 'bail|required',
            'time' => 'bail|required',
            'email_1_time' => 'bail|required',
            'email_2_time' => 'bail|required|numeric|max:' . $email_1_time,
            'email_3_time' => 'bail|required|numeric|max:' . $email_2_time,
            'early_0' => 'bail|required',
            'early_1' => 'bail|required|numeric|max:' . $early_0,
            'early_2' => 'bail|required|numeric|max:' . $early_1,
            'early_3' => 'bail|required|numeric|min:' . $early_2,

        ];
        if ($row_ids != null) {
            foreach ($row_ids as $id) {
                $timesheet_approval_configuration_rules = [
                    'from_' . $id => 'bail|required|',
                    'rating_' . $id => 'bail|required|',
                ];
                $rules = array_merge($rules, $timesheet_approval_configuration_rules);
            }
        }
        return $rules;

    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        $email_1_time = request('email_1_time');
        $email_2_time = request('email_2_time');
        $early_0 = request('early_0');
        $early_1 = request('early_1');
        $early_2 = request('early_2');
        $row_ids = [0,1,2,3,4];
        $message = [
            'day.required' => 'Day is required.',
            'time.required' => 'Time is required.',
            'email_1_time.required' => 'Email 1 Time is required.',
            'email_2_time.required' => 'Email 2 Time is required.',
            'email_3_time.required' => 'Email 3 Time is required.',
            'email_2_time.max' => 'Email 2 should be lessthan ' . $email_1_time ,
            'email_3_time.max' => 'Email 3 should be lessthan ' . $email_2_time ,
            'from_0.required' => 'This field is required',
            'from_1.required' => 'This field is required',
            'from_2.required' => 'This field is required',
            'from_3.required' => 'This field is required',
            'from_4.required' => 'This field is required',
            'early_0.required' => 'This field is required',
            'early_1.required' => 'This field is required',
            'early_2.required' => 'This field is required',
            'early_3.required' => 'This field is required',
            'early_4.required' => 'This field is required',
            'early_1.max' => 'This field should be lessthan '. $early_0,
            'early_2.max' => 'This field should be lessthan '. $early_1,
            'early_3.min' => 'This field should be greaterthan '. $early_2,
            'rating_0.required' => 'Rating field is required',
            'rating_1.required' => 'Rating field is required',
            'rating_2.required' => 'Rating field is required',
            'rating_3.required' => 'Rating field is required',
            'rating_4.required' => 'Rating field is required',
        ];


        return $message;

    }

}
