<?php

namespace Modules\Admin\Http\Requests;

use Config;

class SiteSettingsRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [
            'shift_duration_limit' => 'required|numeric|min:' . Config::get('globals.shift_minimum_duration_limit') . '|max:99',
            'shift_start_time_tolerance' => "bail|integer|min:0|max:200",
            'shift_end_time_tolerance' => "bail|integer|min:0|max:200",
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
        return [
            'shift_duration_limit.required' => 'Shift Duration Limit is required.',
            'shift_duration_limit.numeric' => 'Shift Duration should be a numeric value.',
            'shift_duration_limit.max' => 'Max Value should not be greater than 99.',
            'shift_duration_limit.min' => 'Min Value should not be lesser than ' . Config::get('globals.shift_minimum_duration_limit') . '.',
            'shift_start_time_tolerance.integer' => 'Shift Start Time Tolerance should be a numeric value.',
            'shift_start_time_tolerance.max' => 'Max Value should be lesser than 200.',
            'shift_start_time_tolerance.min' => 'Min Value should not be lesser than 0.',
            'shift_end_time_tolerance.integer' => 'Shift End Time Tolerance should be a numeric value.',
            'shift_end_time_tolerance.max' => 'Max Value should be lesser than 200.',
            'shift_end_time_tolerance.min' => 'Min Value should not be lesser than 0.',
        ];
    }

}
