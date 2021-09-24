<?php

namespace Modules\Contracts\Http\Requests;

use Config;

class ContractExpirySettingsRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $alert_period_1 = request('alert_period_1');
        $alert_period_2 = request('alert_period_2');


        $rules = [
            'alert_period_1' => 'bail|required',
            'alert_period_2' => 'bail|required|numeric|max:' . $alert_period_1,
            'alert_period_3' => 'bail|required|numeric|max:' . $alert_period_2,
            'email_1_time' => 'required',
            'email_2_time' => 'required',
            'email_3_time' => 'required',

        ];

        return $rules;

    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        $id = request('id');
        $alert_period_1 = request('alert_period_1');
        $alert_period_2 = request('alert_period_2');
        $message = [
            'alert_period_1.required' => 'Alert Period 1 is required.',
            'alert_period_2.required' => 'Alert Period 2 is required.',
            'alert_period_3.required' => 'Alert Period 3 is required.',
            'alert_period_2.max' => 'Alert Period 2 should be lessthan ' . $alert_period_1 ,
            'alert_period_3.max' => 'Alert Period 3 should be lessthan ' . $alert_period_2 ,
            'email_1_time.required' => 'Email Time 1 is required.',
            'email_2_time.required' => 'Email Time 2 is required.',
            'email_3_time.required' => 'Email Time 3 is required.',

        ];

        return $message;

    }

}
