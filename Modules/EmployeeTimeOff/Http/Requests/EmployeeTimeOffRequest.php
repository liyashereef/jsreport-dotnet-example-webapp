<?php

namespace Modules\EmployeeTimeOff\Http\Requests;

use Modules\Admin\Models\TimeOffRequestTypeLookup;
use Modules\EmployeeTimeOff\Rules\DateOverlap;

class EmployeeTimeOffRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $employee_id = request('employee_id');
        $start_date = request('start_date');
        $end_date = request('end_date');
        $request_type_id = TimeOffRequestTypeLookup::where('request_type', 'Vacation Request')->value('id');

        $rules = [
            'employee_id' => 'bail|required',
            'customer_id' => 'bail|required',
            'supervisor_id' => 'bail|required',
            'areamanager_id' => 'bail|required',
            'oc_email' => 'bail|nullable|email',
            'request_type_id' => 'bail|required',
            'vacation_pay_required' => 'bail|required_if:request_type_id,==,' . $request_type_id,
            'vacation_pay_amount' => 'bail|required_if:vacation_pay_required,1|nullable|min:0|digits_between:1,10',
            'vacation_payperiod_id' => 'bail|required_if:vacation_pay_required,1',
            'start_date' => ['bail', 'required', 'date', new DateOverlap($id, $employee_id, $start_date, $end_date)],
            'end_date' => ['bail', 'required', 'date', 'after:start_date', new DateOverlap($id, $employee_id, $start_date, $end_date)],
            'no_of_shifts' => 'bail|required|numeric|min:0,|digits_between:1,2',
            'average_shift_length' => 'bail|required|numeric|min:0,|digits_between:1,2',
            'total_hours_away' => 'bail|required|numeric|min:0',
            'leave_reason_id' => 'bail|required',
            'other_reason' => 'bail|required_if:leave_reason_id,!==,0|nullable|max:255',
            'nature_of_request' => 'bail|required|max:1000',
            'request_category_id' => 'bail|required',
            'days_requested' => 'bail|required|numeric|min:0',
            'days_approved' => 'bail|required|numeric|min:0',
            'days_rejected' => 'bail|required|numeric|min:0',
            'days_remaining' => 'bail|required|numeric|min:0',
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
            'employee_id.required' => 'Please choose a employee number',
            'customer_id.required' => 'Please choose a project number',
            'supervisor_id.required' => 'Supervisor details required',
            'areamanager_id.required' => 'Area Manager details required',
            'request_type_id.required' => 'Please choose a request type',
            'vacation_pay_required.required_if' => 'Please choose vacation pay',
            'vacation_pay_amount.required_if' => 'Please enter the pay amount',
            'vacation_pay_amount.min' => 'Minimum pay amount should be zero',
            'vacation_payperiod_id.required_if' => 'Please choose a pay period',
            'start_date.required' => 'Please enter the start date',
            'end_date.required' => 'Please enter the end date',
            'start_date.date' => 'Please enter valid date',
            'end_date.date' => 'Please enter valid date',
            'start_date.after_or_equal' => 'Please enter a date greater than today',
            'end_date.after_or_equal' => 'Please enter a date greater than start date',
            'no_of_shifts.required' => 'Total shifts away is required',
            'no_of_shifts.numeric' => 'Total shifts away should be numeric value',
            'average_shift_length.required' => 'Average Shift Length is required',
            'average_shift_length.numeric' => 'Average Shift Length should be numeric value',
            'total_hours_away.required' => 'Total Hours Away is required',
            'total_hours_away.numeric' => 'Total Hours Away should be numeric value',
            'leave_reason_id.required' => 'Please choose a reason for the request',
            'other_reason.required_if' => 'Please enter a other reason',
            'nature_of_request.required' => 'Please enter the nature of request',
            'request_category_id.required' => 'Please choose a request type',
            'days_requested.required' => 'Please enter the days requested',
            'days_requested.numeric' => 'Days Requested should be numeric value',
            'days_approved.required' => 'Please enter the days approved',
            'days_approved.numeric' => 'Days Approved should be numeric value',
            'days_rejected.required' => 'Please enter the days rejected',
            'days_rejected.numeric' => 'Days Rejected should be numeric value',
            'days_remaining.required' => 'Please enter the days remaining',
            'days_remaining.numeric' => 'Remaining Balance should be numeric value',
            'vacation_pay_amount.digits_between' => 'The vacation pay amount must be between 1 to 10 digits.'
        ];
    }

}
