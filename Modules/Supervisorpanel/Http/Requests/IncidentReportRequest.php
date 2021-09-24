<?php

namespace Modules\Supervisorpanel\Http\Requests;

use Modules\Admin\Repositories\IncidentReportSubjectRepository;

class IncidentReportRequest extends Request
{

    protected $incident_report_repository;

    /**
     * @param IncidentReportSubjectRepository $incident_report_subject_repository
     */
    public function __construct(IncidentReportSubjectRepository $incident_report_subject_repository)
    {
        $this->incident_report_subject_repository = $incident_report_subject_repository;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $id = request('id');
        $custom_subject_rule = [];
        $custom_rules = [];
        $subject_id = request('subject');
        $priority_id = request('priority_id');
        $upload_incident_manually = request('upload_incident_report');
        if ($upload_incident_manually == 1) {
            $custom_rules = ['report_attachment' => 'bail|required|max:20000'];
        }
        $subject_obj = $this->incident_report_subject_repository->get($subject_id);
        if ($subject_obj != null) {
            $subject = $subject_obj->subject;
            if ($subject == 'Others') {
                $custom_subject_rule = ['custom_subject' => 'bail|required|max:1000'];
            }
        }
        $rules = [
            //'description' => 'bail|required|max:1000',
            'subject' => 'bail|required',
            'priority_id' => 'bail|required',
            'title' => 'bail|required|max:255',
            // 'attachement' => 'bail|max:3072',
            'notes' => 'bail|max:255',
            'short_description' => 'bail|max:255',
            'time_of_day' => 'bail|required',
            'date' => 'bail|required|integer|min:1|max:31',
            'month' => 'bail|required',
            'yearvalue' => 'bail|required|digits:4|integer|min:1900',
            //|max:' . (date('Y') + 1), '
            'time' => 'bail|required|date_format:H:i',
            'incident_detail' => 'bail|required|max:4999',
            'upload_incident_report' => 'bail|required',
        ];
        $new_rules = array_merge($rules, $custom_subject_rule);
        $rules2 = array_merge($new_rules, $custom_rules);
        return $rules2;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //'description.required' => 'Description is required.',
            'subject.required' => 'Subject is required.',
            'priority_id.required' => 'Priority is required.',
            'title.required' => 'Title is required.',
            'date.required' => 'Date is required.',
            'date.min' => 'Please input valid date',
            'date.max' => 'Please input valid date.',
            'month.required' => 'Month is required.',
            'yearvalue.required' => 'Year is required.',
            'yearvalue.digits' => 'Please input valid year with 4 digits.',
            'yearvalue.integer' => 'Please input valid year',
            'date.integer' => 'Please input valid date',
            'time.required' => 'Time is required.',
            'time.date_format' => 'Time should be HH:MM format',
            'custom_subject.required' => 'Subject is required.',
            'custom_subject.max' => 'Custom Subject should not exceed 255 characters.',
            'attachement.max' => 'File upload size should not exceed 3MB',
            'incident_detail.max' => 'Details should not exceed 5000 characters',
            'short_description.max'=> 'Short Description should not exceed 255 characters',
            'report_attachment.required' => 'Please upload Incident Report.',
            // 'report_attachment.size' => 'size exceeded',
        ];
    }

}
