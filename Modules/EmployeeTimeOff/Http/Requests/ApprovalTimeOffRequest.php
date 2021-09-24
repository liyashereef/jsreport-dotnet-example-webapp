<?php

namespace Modules\EmployeeTimeOff\Http\Requests;
use Modules\EmployeeTimeOff\Rules\DateOverlap;

class ApprovalTimeOffRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [
         
            'approved' => 'bail|required',
            'notes' => 'bail|nullable|max:1000',
            'days_requested' => 'bail|required|numeric|min:0',
            'days_approved' => 'bail|required|numeric|min:0|lte:days_requested',
            'days_rejected' => 'bail|required|numeric|min:0|lte:days_requested',
            'days_remaining' => 'bail|required|numeric|min:0|',
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
            'approved.required' => 'Please select an action',
            'notes.max' => 'Notes should be less than 1000 characters',
            'days_requested.required' => 'Days requested field is required',
            'days_approved.required' => 'Days approved field is required',
            'days_rejected.required' => 'Days rejected field is required',
            //'days_remaining.required' => 'Days remaining field is required',
            //'days_rejected.lte' => 'Days rejected must be less than or equal to days requested'

        ];
    }
   

}
