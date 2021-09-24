<?php

namespace Modules\Hranalytics\Http\Requests;

use Auth;
use Illuminate\Support\Facades\Input;
use Modules\Hranalytics\Models\Job;
use Session;

class AttachmentRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $value = Input::get('attachment_id');
        $session_obj = Session::get('CANINFO');
        $rule = [];
        $rules = [];
        $rules = ['attachment_file_name.' . $value => 'bail|mimetypes:application/pdf,image/jpeg,image/png,image/jpeg,image/gif,image/svg+xml,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,application/vnd.ms-powerpoint,text/plain,application/vnd.ms-office,application/octet-stream,application/csv,application/excel,
         application/vnd.ms-excel, application/vnd.msexcel|max:3072'];
        if (Auth::user() == null || Auth::user()->role != 'super_user') {
            $job = Job::find($session_obj['job']->id);
            $required_attachments = json_decode($job->required_attachments);
            if (is_array($required_attachments)) {
                foreach ($required_attachments as $key => $values) {
                    if ($values == $value) {
                        $rule['attachment_file_name.' . $value] = 'required';
                    }
                }
                // $rule['attachment_file_name'] = 'required';
                $rules = array_merge($rule, $rules);
            }
        }
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
            'attachment_file_name.required' => 'File attachments are required',
            'attachment_file_name.min' => 'File attachments are required',
            'attachment_file_name.*.required' => 'File attachments are required',
            // 'attachment_file_name.*.mimes' => 'Please upload a file of type image or document',
            'attachment_file_name.*.mimetypes' => 'Please upload a file of type image or document',
            //'attachment_file_name.*.size' => 'The attachment may not be greater than 3B.',
            'attachment_file_name.*.max' => 'The attachments may not be greater than 3MB.',
            /*'attachment_file_name.1.max' => 'The attachments may not be greater than 3MB.',
        'attachment_file_name.2.max' => 'The attachments may not be greater than 3MB.',*/
        ];
    }

}
