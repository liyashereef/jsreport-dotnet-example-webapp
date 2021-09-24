<?php

namespace Modules\Management\Http\Requests;
use Modules\Admin\Http\Requests\CustomerRequest;


class CustomerPreferenceRequest extends CustomerRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $journal_rules = [];
        $interval_check = request('interval_check');
        if ($interval_check == 1) {
            $journal_rules = [
                'guard_tour_duration' => 'bail|required_if:interval_check,==,1|not_in:0|max:2',
            ];
        }
        $overstay_rules = [];
        $overstay_enabled = request('overstay_enabled');
        if ($overstay_enabled == 1) {
            $overstay_rules = [
                'overstay_time' => 'bail|required_if:overstay_enabled,==,1',
            ];
        }
        $employee_rating_rules = [];
        $employee_rating_response = request('employee_rating_response');
        if ($employee_rating_response == 1) {
            $employee_rating_rules = [
                'employee_rating_response_time' => 'bail|min:1|required_if:employee_rating_response,==,1',
            ];
        }
        $qrcode_rules = [];
        $qrlocation_enabled = request('qr_patrol_enabled');
        if ($qrlocation_enabled == 1) {
            $qrcode_rules = [
                'qr_picture_limit' => 'bail|required_if:qr_patrol_enabled,==,1|integer|max:5',
            ];
        }
        $qrcode_interval_rules = [];
        $qrcode_interval_enabled = request('qr_interval_check');
        if ($qrcode_interval_enabled == 1) {
            $qrcode_interval_rules = [
                'qr_duration' => 'bail|required_if:qr_interval_check,==,1|integer|min:1|max:600',
            ];
        }

        $motion_sensor_rules = [];
        $motion_sensor_enabled = request('motion_sensor_enabled');
        if ($motion_sensor_enabled == 1) {
            $motion_sensor_rules = [
                'motion_sensor_incident_subject' => 'bail|required_if:motion_sensor_enabled,==,1|integer',
            ];
        }

        $rules=[
            'basement_mode' => 'nullable',
            'basement_interval' => 'required_if:basement_mode,==,on',
            'basement_noofrounds' => 'required_if:basement_mode,==,on',
            'time_sheet_approver_id' => 'bail|required',
        ];

        $rules = array_merge($rules,
        $journal_rules,
        $overstay_rules,
        $employee_rating_rules,
        $qrcode_rules,
        $qrcode_interval_rules,
        $motion_sensor_rules);

        // $combined_rules = array_merge($overstay_rules, $journal_rules);
        // $combined_all_rules = array_merge($employee_rating_rules, $combined_rules);
        // $rules = array_merge($combined_all_rules,$qrcode_rules,$qrcode_interval_rules);
        return $rules;
    }

    public function messages()
    {

        $msg= [

            'guard_tour_duration.required_if' => 'Please enter the duration',
            'guard_tour_duration.max' => 'Please enter valid duration',
            'overstay_time.required_if' => 'Please select Overstay time',
            'employee_rating_response_time.required_if' => 'Please add response time',
            'qr_duration.required_if'=>'Please enter the duration',
            'qr_duration.integer'=>'Please enter valid duration',
            'qr_duration.max' => 'Maximum duration should be 600 minutes',
            'qr_duration.min' => 'Minimum duration should be 1 minute',
            'qr_picture_limit.required_if'=>'Please enter the limit',
            'qr_picture_limit.max'=>'Picture limit may not be greater than 5',
            'basement_interval.required_if' => 'Mandatory if Basement mode selected',
            'basement_noofrounds.required_if' => 'Mandatory if Basement mode selected',
            'motion_sensor_incident_subject.required_if'=>'Please select incident subject',
            'time_sheet_approver_id.required' => 'Approver name is required',
        ];

        return $msg;
    }
}
