<?php

namespace Modules\Timetracker\Http\Requests;

use Modules\Admin\Http\Requests\Request;

class ManualTimetrackerReportRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $employee = request('employee');
        $payperiod = request('payperiod');
        $week = request('week');
        $customer = request('customer');
        $cpid = request('cpid');
        $functionId = request('functionId');
        $rateId = request('rateId');
        $activityType = request('activityType');
        $activityCode = request('activityCode');
        $hour = request('hour');

        return $rules = [
            'employee' => 'required|not_in:0',
            'payperiod' => 'required|not_in:0',
            'week' => 'required|not_in:0',
            'customer' => 'required|not_in:0',
            'cpid' => 'required|not_in:0',
            'functionId' => 'required',
            'rateId' => 'required',
            'activityType' => 'required|not_in:0',
            'activityCode' => 'required|not_in:0',
            'hour' => 'required'
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
            'employee.required' => 'Employee is required.',
            'employee.not_in' =>'Please choose any one',
            'payperiod.required' => 'Pay Period is required.',
            'payperiod.not_in' =>'Please choose any one',
            'week.required' => 'Week is required.',
            'week.not_in' =>'Please choose any one',
            'customer.required' => 'customer is required.',
            'customer.not_in' =>'Please choose any one',
            'cpid.required' => 'CPID is required.',
            'cpid.not_in' =>'Please choose any one',
            'functionId.required' => 'Function is required.',
            'rateId.required' => 'Rate is required.',
            'activityType.required' => 'Activity Type is required.',
            'activityType.not_in' =>'Please choose any one',
            'activityCode.required' => 'Activity Code is required.',
            'activityCode.not_in' =>'Please choose any one',
            'hour.required' => 'Hour is required.',
            'hour.not_in' =>'Please choose any one',
        ];
    }
}
