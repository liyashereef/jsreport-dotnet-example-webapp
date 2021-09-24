<?php

namespace Modules\Management\Http\Requests;
use Modules\Admin\Http\Requests\UserRequest;


class UserProfileTabRequest extends UserRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $employee_vet_status = request('employee_vet_status');
        $user_role = request('role');
        $rules = [
            'phone' => 'bail|required|max:13',
            'phone_ext' => 'nullable|numeric|digits_between:1,255',
            'cell_no' => 'bail|nullable|max:13',
            'employee_address' => 'bail|nullable|max:255',
            'employee_vet_status' =>'bail|required',
            'employee_city' => 'bail|nullable|max:255',
            'years_of_security' => 'bail|nullable|numeric|digits_between:1,5',
            'employee_work_email' => 'bail|nullable|email|max:255',
            'employee_doj' => 'bail|nullable|date',
            'employee_dob' => 'bail|nullable|date|before:today',
            'current_project_wage' => 'bail|nullable|max:255',
            'being_canada_since' => 'bail|nullable|date|before:today',
            'wage_expectations_from' => 'bail|nullable|numeric|min:0|max:99999999',
            'wage_expectations_to' => 'bail|nullable|numeric|greater_than_field:wage_expectations_from|min:0|max:99999999',
            'incident_report_logo' => 'mimetypes:image/*',
        ];

        if ($employee_vet_status == 1) {
            $veteran_rules = ['vet_service_number' => 'bail|required|max:15',
                'vet_enrollment_date' => 'bail|required|date_format:"Y-m-d"|before:today',
                'vet_release_date' => 'bail|required|date_format:"Y-m-d"|after:vet_enrollment_date'];
            $rules = array_merge($rules, $veteran_rules);
        }

        if ($user_role != 'client') {
            $role_rules = ['work_type_id' => 'bail|required', 'employee_no' => "bail|required|numeric|digits:6|unique:employees,employee_no," . $id . ",user_id,deleted_at,NULL", 'employee_postal_code' => 'bail|required|min:6|max:6'];
            $rules = array_merge($rules, $role_rules);
        }

        return $rules;
    }
    public function messages()
    {

        $message = [
            'employee_postal_code.required' =>'Postal Code is required',
            'employee_vet_status.required'=> 'Employee Vet Status is required.',
            'work_type_id.required'=>'Work Type is required.',
            'employee_no.required' => 'Employee Number is required.',
            'phone.required' => 'Phone is required.',
            'phone.max' => 'Phone number should not exceed 13 characters.',
            'phone_ext.numeric' => 'Ext should be numeric value.',
            'phone_ext.digits_between' => 'Ext should be between 1 to 255 characters.',
            'cell_no.max' => 'Cell number should not exceed 13 characters.',
            'employee_address.max' => 'Employee address should not exceed 255 characters.',
            /*'employee_full_address.max' => 'Employee full address should not exceed 255 characters.',*/
            'employee_city.max' => 'Employee city should not exceed 255 characters.',
            'employee_postal_code.min' => 'Employee postal code should have 6 characters.',
            'employee_postal_code.max' => 'Employee postal code should have 6 characters.',
            'employee_postal_code.required' => 'Employee postal code is required.',
            'employee_work_email.max' => 'Employee work email should not exceed 255 characters.',
            'employee_work_email.email' => 'Enter valid email address',
            'vet_service_number.required' => 'Enter Service Number',
            'vet_enrollment_date.required' => 'Enter Enrollment Number',
            'vet_release_date.required' => 'Enter Release Date',
            'employee_doj.date' => 'Enter a valid date',
            'employee_dob.date' => 'Enter a valid date',
            'employee_dob.before' => 'Enter a date before today',
            'current_project_wage.max' => 'Current project wage should not exceed 255 characters.',
            'wage_expectations_to.greater_than_field' => 'Enter value greater than minimum wage expected',
            'incident_report_logo.mimetypes' => 'Invalid Incident Report Logo',
            'incident_report_logo.dimensions' =>'Image diemsions must be max width 230px and max height 57px',

        ];
        return $message;
    }
}
