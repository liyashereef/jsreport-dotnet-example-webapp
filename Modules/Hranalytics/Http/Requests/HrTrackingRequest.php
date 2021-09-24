<?php

namespace Modules\Hranalytics\Http\Requests;

class HrTrackingRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            // "completion_date" => "bail|required|array|min:1|after_or_equal:today",
            'completion_date.*' => 'bail|nullable|after_or_equal:today',
            // 'notes.*' => 'bail|nullable',
            // 'entered_by_id' => "required|array|min:1",
            // 'entered_by_id.*' => 'bail|required',
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
            'completion_date.*.after_or_equal' => 'Completion date must be today or later',
            // 'entered_by_id.*.required' => 'Please select the user',
        ];
    }

}
