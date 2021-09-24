<?php

namespace Modules\Admin\Http\Requests;

class CustomerRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $nmso_account = request('nmso_account');
        $stc_rules = [];
        if ($nmso_account == 'yes') {
            $stc_rules = [
                'security_clearance_lookup_id' => 'bail|required_if:nmso_account,==,yes',
            ];
        }

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
        $qr_daily_report_rules = [];
        $qr_daily_activity_report_enabled = request('qr_daily_activity_report');
        if ($qr_daily_activity_report_enabled == 1) {
            $qr_daily_report_rules = [
                'qr_recipient_email' => 'bail|required_if:qr_daily_activity_report,==,1|regex:/^([\w+.%]+@[\w.]+\.[A-Za-z]{2,4},?)+$/',
            ];
        }

        $motion_sensor_rules = [];
        $motion_sensor_enabled = request('motion_sensor_enabled');
        if ($motion_sensor_enabled == 1) {
            $motion_sensor_rules = [
                'motion_sensor_incident_subject' => 'bail|required_if:motion_sensor_enabled,==,1|integer',
            ];
        }
    
        $rules = [
            'project_number' => "bail|required|numeric|digits:7|unique:customers,project_number,{$id},id,active,1,deleted_at,NULL",
            'client_name' => "bail|required|max:38",
            'contact_person_name' => 'bail|max:255',
            'contact_person_email_id' => 'nullable|email|max:255',
            'contact_person_phone' => 'nullable|max:13',
            'contact_person_phone_ext' => 'nullable|numeric|digits_between:1,255',
            'contact_person_cell_phone' => 'nullable|max:13',
            'contact_person_position' => 'bail|max:255',
            'requester_name' => 'bail|required|max:255',
            'requester_position' => 'bail|max:255',
            'requester_empno' => 'bail|max:6',
            'address' => 'bail|required|max:255',
            'city' => 'bail|required|max:255',
            'province' => 'bail|required|max:255',
            'postal_code' => 'bail|required|regex:/^[a-zA-Z][0-9][a-zA-Z][0-9][a-zA-Z][0-9]$/|max:6|min:6',
            'billing_address' => 'bail|required',
            'industry_sector_lookup_id' => 'bail|required',
            'region_lookup_id' => 'bail|required',
            'proj_open' => 'sometimes|nullable|date_format:"Y-m-d"',
            'description' => 'bail|max:255',
            'arpurchase_order_no' => 'bail|max:255',
            'arcust_type' => 'bail|max:255',
            //'incident_report_logo' => 'mimetypes:image/*|dimensions:max_width=230,max_height=57'
            'basement_mode' => 'nullable',
            'basement_interval' => 'required_if:basement_mode,==,on',
            'basement_noofrounds' => 'required_if:basement_mode,==,on',
            'incident_report_logo' => 'mimetypes:image/*',
            'fence_interval'=>'required_if:customer_type,==,1|integer|min:1',
            'contractual_visit_unit'=>'required_if:customer_type,==,1',
        ];
        $rules = array_merge($rules,
            $journal_rules,
            $stc_rules,
            $overstay_rules,
            $employee_rating_rules,
            $qrcode_rules,
            $qrcode_interval_rules,
            $qr_daily_report_rules,
            $motion_sensor_rules);
        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        // $qrcode_msg = [];
        // $qrlocation=request('qr-location-row');
        // if($qrlocation){
        //     foreach($qrlocation as $key => $val)
        //     {
        //         $qrcode_msg['qrcode_'.$key.'.required'] = 'Qrcode is required';
        //         $qrcode_msg['location_'.$key.'.required'] = 'Checkpoints is required';
        //         $qrcode_msg['no_of_attempts_'.$key.'.required'] = 'No of attempts is required';
        //         $qrcode_msg['qrcode_active_'.$key.'.required'] = 'Active is required';
        //         $qrcode_msg['picture_enable_disable_'.$key.'.required'] = 'Picture is required';
        //         //$qrcode_msg['picture_mandatory_'.$key.'.required'] = 'Picture is required';
        //         $qrcode_msg['location_enable_disable_'.$key.'.required'] = 'Location is required';
        //     }
        // }
        $msg= [
            'project_number.required' => 'Project number is required',
            'project_number.unique' => 'Project number should be unique',
            'project_number.numeric' => 'Project number should be 7 digit number',
            'project_number.digits' => 'Project number should be 7 digit number',
            'client_name.required' => 'Client name is required',
            'client_name.unique' => 'Client name should be unique',
            'client_name.max' => 'Client name should not exceed 38 characters',
            'contact_person_name.max' => 'Contact person name should not exceed 255 characters',
            'contact_person_email_id.email' => 'Email id should be valid',
            'contact_person_email_id.max' => 'Email id should not exceed 255 characters',
            'contact_person_phone.max' => 'Phone number should not exceed 13 characters',
            'contact_person_phone_ext.numeric' => 'Ext should not contain letters',
            'contact_person_cell_phone.max' => 'Phone number should not exceed 13 characters',
            'contact_person_phone_ext.digits' => 'Ext should not exceed 3 digits',
            'contact_person_position.max' => 'Contact person position should not exceed 255 characters',
            'requester_name.required' => 'Requestor name is required',
            'requester_name.max' => 'Requestor name should not exceed 255 characters',
            'requester_position.max' => 'Requestor position should not exceed 255 characters',
            'requester_empno.max' => 'Requestor employee number should not exceed 6 characters',
            'address.required' => 'Address is required',
            'address.max' => 'Address should not exceed 255 characters',
            'city.required' => 'City is required',
            'city.max' => 'City should not exceed 255 characters',
            'province.required' => 'Province is required',
            'province.max' => 'Province should not exceed 255 characters',
            'postal_code.required' => 'Postal code is required',
            'postal_code.min' => 'Postal code should be 6 digit',
            'postal_code.max' => 'Postal code should be 6 digit',
            'industry_sector_lookup_id.required' => 'Please select an industry',
            'region_lookup_id.required' => 'Please select a region',
            'proj_open.date_format' => 'The project open date does not match the format(yyyy-mm-dd)',
            'description.max' => 'Description should not exceed 255 characters',
            'arpurchase_order_no.max' => 'Purchase order number should not exceed 255 characters',
            'arcust_type.max' => 'Customer type should not exceed 255 characters',
            'security_clearance_lookup_id.required_if' => 'Please select a security clearance',
            'guard_tour_duration.required_if' => 'Please enter the duration',
            'guard_tour_duration.max' => 'Please enter valid duration',
            'overstay_time.required_if' => 'Please select Overstay time',
            'employee_rating_response_time.required_if' => 'Please add response time',
            'incident_report_logo.mimetypes' => 'Invalid Incident Report Logo',
            'incident_report_logo.dimensions' =>'Image diemsions must be max width 230px and max height 57px',
            'basement_interval.required_if' => 'Mandatory if Basement mode selected',
            'basement_noofrounds.required_if' => 'Mandatory if Basement mode selected',
            'fence_interval.required_if' => 'Fence interval is required',
            'contractual_visit_unit.required_if' => 'Contractual visit unit is required',
            'fence_interval.min' => 'Please choose number greater than 0',
            'qr_duration.required_if'=>'Please enter the duration',
            'qr_duration.integer'=>'Please enter valid duration',
            'qr_duration.max' => 'Maximum duration should be 600 minutes',
            'qr_duration.min' => 'Minimum duration should be 1 minute',
            'qr_picture_limit.required_if'=>'Please enter the limit',
            'qr_picture_limit.max'=>'Picture limit may not be greater than 5',
            'qr_recipient_email.required_if'=>'Please enter the email id',
            'motion_sensor_incident_subject.required_if'=>'Please select incident subject',
        ];
        //$combined_msg = array_merge($qrcode_msg, $msg);
        return $msg;
    }

}
