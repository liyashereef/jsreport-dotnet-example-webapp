<?php

namespace Modules\Admin\Http\Requests;

class ScheduleShiftTimingRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $rules = [
            'from' => 'required',
            // 'to' => 'required|greater_than_field:from',
            'displayable' => 'required',
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
            'from.required' => 'This field is required.',
            'to.required' => 'This field is required.',
            // 'to.greater_than_field' => 'This field should be greater or equal to from.',
            'displayable.required' => 'Please select any value',
        ];
    }

}
