<?php

namespace Modules\Recruitment\Http\Requests;

use Modules\Admin\Http\Requests\UserRequest;

class RecEmployeeConversionRequest extends UserRequest
{
     /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $row_ids = request('row-no');
        $certificate_row_ids = request('certificate-row-no');
        $employee_vet_status = request('employee_vet_status');
        $user_role = request('role_id');

        $rules = [
            'first_name' => 'bail|required|regex:/^[0-9A-Za-z\'\s\-]+$/u|max:255' . $id,
            'last_name' => 'bail|required|regex:/^[0-9A-Za-z\'\s\-]+$/u|max:255',
            'role_id' => 'bail|required',
            'phone' => 'bail|required|max:13',
            'phone_ext' => 'nullable|numeric|digits_between:1,255',
            'username' => "bail|required|max:255|unique:users,username," . $id . ",id,deleted_at,NULL",
            'email' => "bail|required|max:255|email|unique:users,email," . $id . ",id,deleted_at,NULL",
            'password' => 'required_if:id,""|nullable|min:8,',
            'cell_no' => 'bail|nullable|max:13',
            'employee_address' => 'bail|required|max:255',
            /*'employee_full_address' => 'bail|nullable|max:255',*/
            'employee_city' => 'bail|required|max:255',
            'years_of_security' => 'bail|nullable|numeric|digits_between:1,5',
            'employee_work_email' => 'bail|nullable|email|max:255',
            'employee_doj' => 'bail|required|date',
            'employee_dob' => 'bail|required|date|before:today',
            'current_project_wage' => 'bail|nullable|max:255',
            'being_canada_since' => 'bail|nullable|date|before:today',
            'wage_expectations_from' => 'bail|nullable|numeric|min:0|max:99999999',
            'wage_expectations_to' => 'bail|nullable|numeric|greater_than_field:wage_expectations_from|min:0|max:99999999',
            //'max_allowable_expense' =>'numeric',
            'reporting_to_id' => 'bail|required',
             'project_no' => 'bail|required',
            'project_deployed' => 'bail|required',
            'policy_file' => 'bail|required|mimetypes:application/pdf,image/jpeg,image/png,image/jpeg,image/gif,image/svg+xml,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,application/vnd.ms-powerpoint,text/plain,application/vnd.ms-office,application/octet-stream,application/csv,application/excel,
        application/vnd.ms-excel, application/vnd.msexcel|max:3072',
            //Emergency contact section
            // 'name' => 'required',
            // 'full_address' => 'required'
              'sin'=>"bail|required|digits:9|regex:/^[1-9][0-9]{8,}/|unique:users,sin," . $id . ",id,deleted_at,NULL",
         'bankid' => 'required',
            'bankcode' => 'required',
            'transit' => 'required|max:150|regex:/^[a-zA-Z0-9 .\-\)\(\[\]]*$/',
            'account_no' => "required|regex:/^[a-zA-Z0-9 .\-\)\(\[\]]*$/|unique:user_banks,account_no," . $id . ",user_id,deleted_at,NULL",
            'payment_method_id' => 'required',
            //Benifits section
            'payroll_group_id' => 'required',
            'vacation_level' => 'required|numeric|max:100',
            'federal_td1_claim' => 'nullable|max:150|regex:/^[a-zA-Z0-9 .\-\)\(\[\]]*$/',
            'provincial_td1_claim' => 'nullable|max:150|regex:/^[a-zA-Z0-9 .\-\)\(\[\]]*$/',
            'epaystub_email' => 'nullable|email',
            //'is_lacapitale_life_insurance_enrolled' => 'required',
            //Employment section
            // 'continuous_seniority' => 'required',
            'pay_detach_customer_id' => 'required',
            'tax_province' => 'nullable|regex:/^[a-zA-Z0-9 .\-\)\(\[\]]*$/|max:100',
            //Emergency contact section
            'name' => 'nullable|max:100',
            'full_address' => 'nullable|max:100'
        ];
        if ($user_role != 'client') {
            $role_rules = ['work_type_id' => 'bail|required',
            'employee_no' => "bail|required|numeric|digits:6|unique:employees,employee_no," . $id . ",user_id,deleted_at,NULL",
            'employee_postal_code' => 'bail|required|min:6|max:6'];
            $rules = array_merge($rules, $role_rules);
        }
        if ($employee_vet_status == 1) {
            $veteran_rules = ['vet_service_number' => 'bail|required|max:15',
                'vet_enrollment_date' => 'bail|required|date_format:"Y-m-d"|before:today',
                'vet_release_date' => 'bail|required|date_format:"Y-m-d"|after:vet_enrollment_date'];
            $rules = array_merge($rules, $veteran_rules);
        }

        if ($row_ids != null) {
            foreach ($row_ids as $id) {
                $security_clearance_rules = [
                    'security_clearance_' . $id => 'bail|nullable',
                    'valid_until_' . $id => 'bail|required_with:security_clearance_' . $id . '|nullable|date',
                ];
                $rules = array_merge($rules, $security_clearance_rules);
            }
        }

        if ($certificate_row_ids != null) {
            foreach ($certificate_row_ids as $id) {
                $certificate_rules = [
                    'certificate_' . $id => 'bail|nullable',
                    'expiry_' . $id => 'bail|required_with:certificate_' . $id . '|nullable|date',
                ];
                $rules = array_merge($rules, $certificate_rules);
            }
        }

        return $rules;
        $rules1 = parent::rules();
        $rules2 = [
            'project_no' => 'bail|required',
            'project_deployed' => 'bail|required',
            'policy_file' => 'bail|required|mimetypes:application/pdf,image/jpeg,image/png,image/jpeg,image/gif,image/svg+xml,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,application/vnd.ms-powerpoint,text/plain,application/vnd.ms-office,application/octet-stream,application/csv,application/excel,
        application/vnd.ms-excel, application/vnd.msexcel|max:3072',
        ];
        $rules = array_merge($rules1, $rules2);

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {

         $row_ids = request('row-no');
        $certificate_row_ids = request('certificate-row-no');

        $message = [
            'provincial_td1_claim.alpha_num'=>'This field only accept letters and digits',
            'federal_td1_claim.alpha_num'=>'This field only accept letters and digits',
            'gender.required'=>'Please choose gender',
            'sin.required'=>'SIN is required',
            'sin.digits'=>'SIN should be 9 digits',
            'sin.unique'=>'SIN should be unique',
            'transit.required'=>'Transit is required',
            'account_no.required'=>'Account number is required',
            'pay_detach_customer_id.required'=>'Please choose customer',
            'account_no.unique'=>'Account number should be unique',
            'payment_method_id.required'=>'Please select payment method',
            'payroll_group_id.required'=>'Please select payroll group',
            'vacation_level.required'=>'Please enter vacation percentage',
            'bankid.required'=>'Bank Name is required',
            'bankcode.required'=>'Bank Code is required',
            'first_name.required' => 'Name is required.',
            'first_name.regex' => 'Please enter only characters.',
            'last_name.required' => 'Last name is required',
            'last_name.regex' => 'Please enter only characters.',
            'username.required' => 'Unique user name is required.',
            'email.required' => 'Unique Email is required.',
            'password.required_if' => 'Password is required.',
            'password.min' => 'Password with minimum 8 character is required.',
            'role_id.required' => 'Role is required.',
            'work_type_id.required' => 'Work type is required.',
            'employee_no.required' => 'Employee Number is required.',
            'phone.required' => 'Phone is required.',
            'phone.max' => 'Phone number should not exceed 13 characters.',
            'phone_ext.numeric' => 'Ext should be numeric value.',
            'phone_ext.digits_between' => 'Ext should be between 1 to 255 characters.',
            'cell_no.max' => 'Cell number should not exceed 13 characters.',
            'employee_address.required' => 'Address is required',
            'employee_address.max' => 'Employee address should not exceed 255 characters.',
            /*'employee_full_address.max' => 'Employee full address should not exceed 255 characters.',*/
            'employee_city.required' => 'City is required',
            'employee_city.max' => 'Employee city should not exceed 255 characters.',
            'employee_postal_code.min' => 'Employee postal code should have 6 characters.',
            'employee_postal_code.max' => 'Employee postal code should have 6 characters.',
            'employee_postal_code.required' => 'Employee postal code is required.',
            'employee_work_email.max' => 'Employee work email should not exceed 255 characters.',
            'employee_work_email.email' => 'Enter valid email address',
            'employee_doj.required' => 'DOJ is required',
            'employee_doj.date' => 'Enter a valid date',
            'employee_dob.required' => 'DOB is required',
            'employee_dob.date' => 'Enter a valid date',
            'employee_dob.before' => 'Enter a date before today',
            'current_project_wage.max' => 'Current project wage should not exceed 255 characters.',
            'vet_service_number.required' => 'Enter Service Number',
            'vet_enrollment_date.required' => 'Enter Enrollment Number',
            'vet_release_date.required' => 'Enter Release Date',
            'wage_expectations_to.greater_than_field' => 'Enter value greater than minimum wage expected',
            //'max_allowable_expense' =>'Enter a numeric value',
            'reporting_to_id.required' => 'Approver is required.',
            'policy_file.required' => '',
            'project_no.required' => 'Project no is required',
            'project_deployed.required' => 'please choose projet deploy date',
            'policy_file.mimetypes' => 'Upload image or document file',
        ];

        if ($row_ids != null) {
            foreach ($row_ids as $id) {
                $security_clearance_rules = [
                'security_clearance_' . $id . '.not_in' => 'Please choose a security clearance.',
                'valid_until_' . $id . '.date' => 'Enter a valid date.',
                'valid_until_' . $id . '.required_with' => 'Enter valid until date.',
                ];
                $message = array_merge($message, $security_clearance_rules);
            }
        }
        if ($certificate_row_ids != null) {
            foreach ($certificate_row_ids as $id) {
                $certificate_rules = [
                'certificate_' . $id . '.not_in' => 'Please choose a certificate.',
                'valid_until_' . $id . '.date' => 'Enter a valid date.',
                'expiry_' . $id . '.required_with' => 'Enter valid until date.',
                ];
                $message = array_merge($message, $certificate_rules);
            }
        }

            return $message;
    }
}
