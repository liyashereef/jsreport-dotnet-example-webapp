<?php

namespace Modules\Contracts\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Uploadcmufform extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules["rfc_document_attachment"] = "required";
        $rules['division_lookup'] = 'required_if:multidivision,1';
        $rules['master_customer'] = 'required_if:masterentity,1';
        $rules['customer_client'] = "required";
        $rules['contract_number'] = "required";
        $rules['area_manager_text'] = "required";
        $rules['reason_for_submission'] = "required";
        $rules['business_segment'] = "required";
        $rules['line_of_business'] = "required";
        $rules['area_manager'] = "required";
        $rules['area_manager_position_text'] = 'required';
        $rules['area_manager_email_address'] = "required|email";
        $rules['area_manager_office_number'] = "required";
        $rules['area_manager_cell_number'] = "required";
        $rules['office_address'] = "required";
        $rules['sales_employee_id'] = 'required';
        $rules['sales_contact_job_title'] = 'required';
        $rules['sales_contact_emailaddress'] = 'required|email';
        $rules['sales_contact_office_number'] = 'required';
        $rules['sales_contact_cell_number'] = 'required';
        $rules['sales_contact_division'] = 'required';
        $rules['sales_contact_office_address'] = 'required';
        $rules['primary_contact'] = 'required';
        $rules['contact_name'] = 'required';
        $rules['contact_jobtitle'] = 'required';
        $rules['contact_emailaddress'] = 'required|email';
        $rules['contact_phoneno'] = 'required';
        $rules['contact_cellno'] = 'required';
        $rules['contract_startdate'] = 'required|date';
        $rules['contract_length'] = 'required|min:0|max:9999999999';
        $rules['contractonourtemplate'] = 'required';
        $rules['contract_enddate'] = 'required|date';
        $rules['renewable_contract'] = 'required';
        $rules['contract_length_renewal_years'] = 'numeric';
        $rules['terminationnoticeperiodclient'] = 'numeric|min:0|required_if:termination_clause_client,1';
        $rules['terminationnoticeperiod'] = 'numeric|min:0|required_if:termination_clause,1';
        $rules['poemail'] = 'nullable|email';
        $rules['billing_ratechange'] = 'required';
        $rules['contract_annualincrease_allowed'] = 'required';
        $rules['contract_billing_cycle'] = 'required';
        $rules['contract_payment_method'] = 'required';
        $rules['ponumber'] = 'required|alpha_num';
        $rules['pocompanyname'] = 'required';
        $rules['scopeofwork'] = 'required';
        $rules['employeeemailaddress'] = 'nullable|email';

        $rules['total_annual_contract_billing'] = 'nullable|numeric|min:0|max:9999999999';
        $rules['total_annual_contract_wages_benifits'] = 'nullable|numeric|min:0|max:9999999999';
        $rules['total_annual_expected_contribution_margin'] = 'nullable|numeric|min:0|max:9999999999';
        $rules['total_hours_perweek'] = 'nullable|numeric|min:0|max:9999999999';

        $rules['average_billrate'] = 'nullable|numeric|min:0|max:9999999999';
        $rules['average_wagerate'] = 'nullable|numeric|min:0|max:9999999999';
        $rules['average_markup'] = 'nullable|numeric|min:0|max:9999999999';
        $rules['delivery_hours'] = 'nullable|numeric|min:0|max:9999999999';
        $rules['contract_length_renewal_years'] = 'nullable|numeric|min:0|max:9999999999';

        $rules['supervisoremployeenumber'] = 'required_if:supervisorassigned,1';

        return $rules;

    }

    public function messages()
    {
        return [
            'cmuf_contract_document.required' => 'Contract document is mandatory ',
            'cmuf_contract_document.mimes' => 'Only allow specified type of files.',
            'customer_client.required' => 'Contract is mandatory.',
            'contract_number.required' => 'Contract number is mandatory.',
            'area_manager_text.required' => 'Regional manager is mandatory.',
            'reason_for_submission.required' => 'Reason for submission is mandatory.',

            'business_segment.required' => 'Business segment is mandatory.',
            'line_of_business.required' => 'Line of business is mandatory.',
            'division_lookup.required' => 'Lead division is mandatory.',
            'division_lookup.required_if' => 'Lead division is when it is a multi division contract.',
            'master_customer.required' => 'Parent customer is mandatory.',
            'master_customer.required_if' => 'Parent customer is mandatory.',
            'area_manager.required' => 'Regional manager is mandatory.',
            'area_manager_position_text.required' => 'Regional manager position is mandatory.',
            'area_manager_email_address.required' => 'Regional manager email address is mandatory.',
            'area_manager_office_number.required' => 'Regional manager office number is mandatory.',
            'area_manager_cell_number.required' => 'Regional manager cell number is mandatory.',
            'office_address.required' => 'Regional manager office address is mandatory.',
            'sales_employee_id.required' => 'Who won contract is mandatory.',
            'sales_contact_job_title.required' => 'Position title is mandatory.',
            'sales_contact_emailaddress.required' => 'Email address is mandatory.',
            'sales_contact_emailaddress.email' => 'Must be a valid Email address.',
            'contact_emailaddress.email' => 'Must be a valid Email address.',
            'sales_contact_office_number.required' => 'Office number is mandatory.',
            'sales_contact_cell_number.required' => 'Cell number is mandatory.',
            'sales_contact_division.required' => 'Division is mandatory.',
            'sales_contact_office_address.required' => 'Office address is mandatory.',
            'primary_contact.required' => 'Client contact is mandatory.',
            'contact_name.required' => 'Contact name is mandatory.',
            'contact_jobtitle.required' => 'Position is mandatory.',
            'contact_emailaddress.required' => 'Email address is mandatory.',
            'contact_phoneno.required' => 'Phone no is mandatory.',
            'contact_cellno.required' => 'Cell number is mandatory.',

            'contract_startdate.required' => 'Start date is mandatory.',
            'contract_startdate.date' => 'Invalid date format.Should be yyyy-mm-dd',
            'contract_length.required' => 'Length is mandatory.',
            'contract_length.max' => 'Maximum 10 digits.',
            'contract_enddate.required' => 'End date name is mandatory.',
            'contract_enddate.date' => 'Invalid date format.Should be yyyy-mm-dd',
            'renewable_contract.required' => 'Renewable flag is mandatory .',
            'terminationnoticeperiodclient.required' => 'Termination notice period is mandatory .',
            'terminationnoticeperiod.required' => 'Termination notice period is mandatory .',
            'terminationnoticeperiodclient.min' => 'Termination notice period should not be less than zero .', 
            'terminationnoticeperiod.min' => 'Termination notice period should not be less than zero .',          
            'contract_length_renewal_years.required' => 'Renewable length is mandatory.',
            'contract_length_renewal_years.numeric' => 'Contracts length is numeric',
            'billing_ratechange.required' => 'Billing rate change is mandatory.',
            'contract_annualincrease_allowed.required' => 'Annual increase rate is mandatory.',
            'contract_annualincrease_allowed.numeric' => 'Annual increase must be a numeric value.',
            'contract_annualincrease_allowed.max' => 'Maximum 10 digits.',
            'poemail.email' => 'Must be a valid email address',

            'rfc_pricing_template.required' => 'RFP Template file is mandatory.',
            'rfc_pricing_template.mimes' => 'RFP Template only allow specified type.',

            'contract_billing_cycle.required' => 'Billing frequency is mandatory.',
            'contract_payment_method.required' => 'Payment method is mandatory.',

            'ponumber.required' => 'Purchase order number is mandatory',
            'pocompanyname.required' => 'Purchase order company name is mandatory',
            'scopeofwork.required' => 'Scope of work is mandatory',

            'po_upload.mimetypes' => 'Purchase order only allow specified type.',
            'ponumber.alpha_num' => 'PO Number accepts only Alpha numeric. Eg : PO Number as PO4567',
            'amendment_attachment_id.mimetypes' => 'Amendment only allow specified type.',
            'rfc_document_attachment.required' => 'RFP Document required',
            'employeeemailaddress.email' => 'Must be a valid email address',

            'total_annual_contract_billing.max' => 'Maximum 10 digits',
            'total_annual_contract_wages_benifits.max' => 'Maximum 10 digits',
            'total_annual_expected_contribution_margin.max' => 'Maximum 10 digits',
            'average_billrate.max' => 'Maximum 10 digits',
            'average_wagerate.max' => 'Maximum 10 digits',
            'average_markup.max' => 'Maximum 10 digits',
            'delivery_hours.max' => 'Maximum 10 digits',
            'contract_length_renewal_years.max' => 'Maximum 10 digits',
            'total_hours_perweek.max' => 'Maximum 10 digits',
            'supervisoremployeenumber.required_if' => 'Employee number is mandatory',
            'contractonourtemplate.required' => 'Contract on our template is mandatory',
            
        ];
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
