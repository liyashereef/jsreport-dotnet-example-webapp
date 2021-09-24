<?php

namespace Modules\Management\Http\Requests;
use Modules\Admin\Http\Requests\CustomerRequest;


class CustomerProfileRequest extends CustomerRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
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
           
        ];
      
        return $rules;
    }
    public function messages()
    {
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
            
        ];
        return $msg;
    }
}