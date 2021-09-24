<?php

namespace Modules\Admin\Http\Requests;

class ScheduleMaximumHourRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $rules = [
            'hour' => "bail|required|integer|between:0,24",
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
            'hour.required' => 'Maximum Hour is required.',
            'hour.integer' => 'Please enter as Hour.',
        ];
    }

}
