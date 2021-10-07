<?php

namespace Modules\Hranalytics\Http\Requests;

use Illuminate\Support\Facades\Input;

class EventLogRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $status = request('status');
        $rules = [
            'status' => 'bail|required',
            'accepted_rate' => ['nullable', 'numeric', 'regex:/^\d*(\.\d{1,3})?$/', 'between:1,9999999999'],
            'status_notes' => 'bail|required|max:500',
            'requirement_id' => 'bail|required|numeric',
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
            'time.date_format' => 'Please enter the time in HH:MM Format.',
            'status_notes.max' => 'Please enter the notes within 50 characters.',
            'status_notes.required' => 'Please enter the notes',
            'status.required' => 'Please Select the Status',
            'project_number.required' => 'Please Choose Project Number',
            'time_scheduled.date_format' => 'Time Scheduled should match HH:MM Format',
            'accepted_rate.numeric' => 'Accepted Rate should be Numeric value',
            'accepted_rate.regex' => 'Accepted Rate should have maximum 3 decimals',
            'accepted_rate.between' => 'Accepted Rate should be maximum 10 digits',

        ];
    }

}
