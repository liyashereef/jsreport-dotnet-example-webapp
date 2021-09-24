<?php

namespace Modules\Admin\Http\Requests;

class CustomerShiftRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        return $rules = [
            'customer_id' => 'bail|required',
            'shiftname' => 'bail|required',
            'starttime' => 'bail|required',
            'endtime' => 'bail|required',
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
            'customer_id.required' => 'customer is required.',
            'shiftname.required' => 'Shift Name is required.',
            'starttime.required' => 'Start Time is required.',
            'endtime.required' => 'End Time is required.',
        ];
    }

}
