<?php

namespace Modules\Contracts\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Contracts\Rules\PointsSum;

class RfpDetailsRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $points = request('points');
        $criteria_name = request('criteria_name');
        return [
            'rfp_response_type_id' => 'required|exists:rfp_response_type_lookups,id',
            'employee_id' => 'required|max:100',
            'rfp_site_name' => 'required|max:100',
            'rfp_site_address' => 'required|max:150',
            'rfp_site_city' => 'required|max:150',
            'rfp_site_postalcode' => 'required|regex:/^[a-zA-Z][0-9][a-zA-Z][0-9][a-zA-Z][0-9]$/|max:6|min:6',
            'rfp_published_date' => 'required|date_format:"Y-m-d"',
            'site_visit_available' => 'required',
            'site_visit_deadline' => 'bail|required_if:site_visit_available,1|nullable|date_format:"Y-m-d"',
            'q_a_deadline_available' => 'required',
            'qa_deadline' => 'bail|required_if:q_a_deadline_available,1|nullable|date_format:"Y-m-d"',
            // 'submission_deadline' => 'required|date_format:"Y-m-d"',
            'announcement_date' => 'required|date_format:"Y-m-d"',
            'project_start_date' => 'required|date_format:"Y-m-d"',
            'submission_label_name.*' => 'required|distinct|max:100',
            'submission_label_value.*' => 'required|date_format:"Y-m-d"',
            'execution_label_name.*' => 'required|distinct|max:100',
            'execution_label_value.*' => 'required|date_format:"Y-m-d"',
            'rfp_contact_name' => 'required|max:100',
            'rfp_contact_title_available' => 'required',
            'rfp_contact_title' => 'bail|required_if:rfp_contact_title_available,1|nullable|max:100',
            'rfp_contact_address_available' => 'required',
            'rfp_contact_address' => 'bail|required_if:rfp_contact_address_available,1|nullable|max:150',
            'rfp_phone_number_available' => 'required',
            'rfp_phone_number' => 'bail|required_if:rfp_phone_number_available,1|nullable|max:14',
            'rfp_email_available' => 'required',
            'rfp_email' => 'bail|required_if:rfp_email_available,1|nullable|email',
            'total_annual_hours' => 'required|numeric',
            'scope_summary' => 'required|max:1000',
            'force_required' => 'required',
            'option_renewal' => 'required|numeric',
            'site_unionized' => 'required',
            'term' => 'required|numeric',
            'union_name' => 'required|max:100',
            'summary_notes' => 'max:150',
            'criteria_name.*' => 'required|max:150',
            //'points.*' => 'required|numeric|sum_of_field:points',
            'points.*' => ['bail', 'required', 'numeric', new PointsSum($id, $criteria_name, $points)],
            'notes.*' => 'required|max:150',
            'union_name' => 'bail|required_if:site_unionized,==,1',

        ];
    }

    public function messages()
    {
        return [
            'rfp_response_type_id.exists' => 'The selected RFP response type is invalid',
            'submission_label_name.*.distinct' => 'Submission Label name must be unique',
            'execution_label_name.*.distinct' => 'Execution Label name must be unique',
            'employee_name.required' => 'Employee name is mandatory ',
            'employee_name.max' => 'Employee name should not exceed 100 characters ',
            'rfp_site_name.required' => 'RFP Site name is mandatory ',
            'rfp_site_name.max' => 'RFP Site name should not exceed 100 characters ',
            'rfp_site_address.required' => 'RFP Site Address is mandatory ',
            'rfp_site_address.max' => 'RFP Site Address should not exceed 150 characters ',
            'rfp_site_city.required' => 'RFP Site City is mandatory ',
            'rfp_site_city.max' => 'RFP Site City should not exceed 150 characters ',
            'rfp_site_postalcode.required' => 'RFP Site Postalcode is mandatory ',
            'rfp_published_date.required' => 'RFP Published Date is mandatory',
            'rfp_published_date.date_format' => 'RFP Published Date should be of format "Y-m-d" ',
            'site_visit_deadline.required_if' => 'Site Visit deadline is mandatory',
            'site_visit_deadline.date_format' => 'Site Visit deadline should be of format "Y-m-d" ',
            'qa_deadline.required_if' => 'QA deadline is mandatory',
            'qa_deadline.date_format' => 'QA deadline should be of format "Y-m-d" ',
            'submission_deadline.required' => 'Submission deadline is mandatory',
            'submission_deadline.date_format' => 'Submission deadline should be of format "Y-m-d" ',
            'announcement_date.required' => 'Announcement date is mandatory',
            'announcement_date.date_format' => 'Announcement  date should be of format "Y-m-d" ',
            'project_start_date.required' => 'Project start date is mandatory',
            'project_start_date.date_format' => 'Project start date should be of format "Y-m-d" ',
            'submission_label_name.*.required' => 'Submission label name is mandatory',
            'submission_label_name.*.max' => 'Submission label name should not exceed 100 characters ',
            'submission_label_value.*.required' => 'Submission label value is mandatory',
            'submission_label_value.*.date_format' => 'Submission label value should be of format "Y-m-d"',
            'execution_label_name.*.required' => 'Execution label name is mandatory',
            'execution_label_name.*.max' => 'Execution label name should not exceed 100 characters ',
            'execution_label_value.*.required' => 'Execution label value is mandatory',
            'execution_label_value.*.date_format' => 'Execution label value should be of format "Y-m-d"',
            'rfp_contact_name.required' => 'RFP Contact name is mandatory ',
            'rfp_contact_name.max' => 'RFP Contact name should not exceed 100 characters ',
            'rfp_contact_title.required_if' => 'RFP Contact title is mandatory ',
            'rfp_contact_title.max' => 'RFP Contact title should not exceed 100 characters ',
            'rfp_contact_address.required_if' => 'RFP Contact address is mandatory ',
            'rfp_contact_address.max' => 'RFP Contact address should not exceed 150 characters ',
            'rfp_phone_number.required_if' => 'RFP Contact number is mandatory ',
            'rfp_phone_number.max' => 'RFP Contact number should not exceed 10 digits ',
            'rfp_email.required_if' => 'RFP email is mandatory ',
            'rfp_email.email' => 'RFP email should be an email',
            'total_annual_hours.required' => 'Total annual hours is mandatory ',
            'total_annual_hours.numeric' => 'Total annual hours should be a number ',
            'scope_summary.required' => 'Scope summary is mandatory ',
            'scope_summary.max' => 'Scope summary should not exceed 1000 characters ',
            'option_renewal.required' => 'Option renewal is mandatory ',
            'option_renewal.numeric' => 'Option renewal should be a number',
            'term.required' => 'Term is mandatory ',
            'term.numeric' => 'Term should be a number',
            'union_name.required' => 'Term is mandatory ',
            'union_name.max' => 'Union name should not exceed 100 characters',
            'summary_notes.required' => 'Summary notes is mandatory ',
            'summary_notes.max' => 'Summary notes should not exceed 150 characters',
            'criteria_name.*.required' => 'Criteria name is mandatory ',
            'criteria_name.*.max' => 'Criteria name should not exceed 150 characters',
            'points.*.required' => 'Points are mandatory ',
            'points.*.max' => 'Points should be numeric',
            'points.*.numeric' => 'Points should be a number',
            'notes.*.required' => 'Notes are mandatory ',
            'notes.*.max' => 'Notes should not exceed 150 characters',
            'union_name.required_if' => 'Union name is required',

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
