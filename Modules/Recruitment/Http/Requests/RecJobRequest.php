<?php

namespace Modules\Recruitment\Http\Requests;

use Illuminate\Support\Facades\Input;

class RecJobRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $permanent_id = request('permanent_id');
        $rules = [
            'reason_id' => 'bail|required',
            //'job_description' => 'bail|required|max:10000',
            //'job_full_description' => 'bail|required|max:10000',
            //'requisition_date' => 'bail|required|date|before_or_equal:today',
            "resign_id" => "required_if:permanent_id,11",
            "terminate_id" => "required_if:permanent_id,12",
            'am_email' => 'bail|required|email|max:255',
            'email' => 'sometimes|nullable|email|max:255',
            'area_manager' => 'bail|required|regex:/^[a-zA-Z ]+$/u|max:50',
            'requester' => 'sometimes|nullable|regex:/^[a-zA-Z ]+$/u|max:50',
            'phone' => 'sometimes|nullable|max:13|min:13',
            'assignment_type_id' => 'bail|required',
            'employee_num' => 'bail|required|numeric|digits:6',
            'position' => 'bail|required|max:255',
            'time' => 'bail|required',
            'required_job_start_date' => 'bail|required|date|after_or_equal:today',
            'end' => 'sometimes|nullable|after_or_equal:required_job_start_date',
            'course' => 'max:255',
            'notes' => 'sometimes|nullable|max:500',
            'ongoing' => 'bail|required',
            'open_position_id' => 'bail|required',
            'no_of_vaccancies' => 'bail|required|numeric|max:999999',
            'training_id' => 'bail|required',
            'training_time' => 'bail|required|numeric',
            'training_timing_id' => 'bail|required',
            'course' => 'sometimes|nullable|max:100',
            'remarks' => 'sometimes|nullable|max:500',
            'wage' => 'bail|required|numeric|max:999999',
            'shifts' => 'bail|required',
            'days_required' => 'bail|required',
            'experiences' => 'bail|required|max:500',
            'vehicle' => 'bail|required',
            'customer_id' => 'bail|required',
            'total_experience'=>'bail|required|numeric|max:100',
            'hours_per_week'=>'bail|required|numeric|max:168',

        ];
        $experiences = Input::get('experiences');
        foreach ($experiences as $key => $experience) {
            if ($experience['experience_id'] != null) {
                $rules['experiences.' . $key . '.year'] = 'bail|required|numeric|max:100';
            }
        }

        $op = strip_tags(Input::get('job_description'));
        $clean_description = str_replace("&nbsp;", " ", $op);
        if (strlen($clean_description) < 50 && !null) {
            $rules['job_description'] = 'bail|min:50|max:10000';
        } else {
            $rules['job_description'] = 'bail|required|min:50|max:10000';
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
            'resign_id.required_if'=>'This field is required',
            'terminate_id.required_if'=>'This field is required',
            'reason_id.required' => 'Please choose the reason.',
            'job_description.required' => 'Please enter the job description.',
            'area_manager.required' => 'Please enter the area manager name.',
            'area_manager.max' => 'Please enter the area manager name within 50 characters.',
            'requester.max' => 'Please enter the requestor name within 50 characters.',
            'phone.min' => 'Please enter a valid phone number.',
            'phone:max' => 'Please enter a valid phone number.',
            'am_email.required' => 'Please enter the area manager email.',
            'email.email' => 'Please enter the valid email.',
            'position.required' => 'Please choose the requestor\'s position.',
            'employee_num.required' => 'Please enter the requestor\'s employee number.',
            'employee_num.min' => 'Please enter the requestor\'s employee number with 6 didgits.',
            'employee_num.max' => 'Please enter the requestor\'s employee number with 6 didgits.',
            'notes.max' => 'Please enter the note within 500 characters.',
            'course.max' => 'Please enter the course within 100 characters.',
            'remarks.max' => 'Please enter the remarks within 500 characters.',
            'assignment_type_id.required' => 'Please choose the type of assignment.',
            'time.required' => 'Please enter the time.',
            'time.date_format' => 'Please enter the time in hh:mm am/pm Format.',
            'ongoing.required' => 'Please choose the option for ongoing permanent position.',
            'training_id.required' => 'Please choose the option for training.',
            'training_time.required' => 'Please enter the training hours.',
            'training_timing_id.required' => 'Please choose the option for client onboarding.',
            'required_job_start_date.required' => 'Please enter the start date.',
            'required_job_start_date.before_or_equal' => 'The start date should be less than or equal to the end date.',
            'end.after_or_equal' => 'The end date should be greater than or equal to the start date.',
            'time:date_format' => 'Please enter a valid time(e.g., 12:00 AM).',
            'days_required.required' => 'Minimum one day is required',
            'shifts.required' => 'Minimum one shift is required',
            'wage.required' => 'Please enter the wage',
            'criteria.required' => 'Please choose the position requirements',
            'customer_id.required' => 'Please enter the project number',
            'experiences.required' => 'Please choose the required experience',
            'experiences.*.year.required' => 'Please enter years of experience.',
            'experiences.*.year.max' => 'Please enter valid years of Experience.',
            'experiences.*.year.numeric' => 'Please enter valid years of Experience.',

        ];
    }
}
