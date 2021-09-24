<?php

namespace Modules\Admin\Http\Requests;

class ParentCustomerRequest extends Request
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
        

        $journal_rules = [];
        $interval_check = request('interval_check');
        
        $overstay_rules = [];
        $overstay_enabled = request('overstay_enabled');
        
        $rules = [
            'project_number' => "bail|required|numeric|digits:7|unique:customers,project_number,{$id},id,active,1,deleted_at,NULL",
            'client_name' => "bail|required|max:255",
        ];

        
        $combined_rule_set = array_merge($journal_rules, $stc_rules);
        $combined_rules = array_merge($overstay_rules, $combined_rule_set);
        $rules = array_merge($rules, $combined_rules);

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
            'project_number.required' => 'Project number is required',
            'project_number.unique' => 'Project number should be unique',
            'project_number.numeric' => 'Project number should be 7 digit number',
            'project_number.digits' => 'Project number should be 7 digit number',
            'client_name.required' => 'Client name is required',
            'client_name.unique' => 'Client name should be unique',
            'client_name.max' => 'Client name should not exceed 255 characters',
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
        ];
    }

}
