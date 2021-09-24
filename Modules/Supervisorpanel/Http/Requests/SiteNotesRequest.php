<?php

namespace Modules\Supervisorpanel\Http\Requests;

use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use  Modules\Supervisorpanel\Repositories\SiteNoteRepository;


class SiteNotesRequest extends Request
{

    protected $incident_report_repository;
    protected $customer_employee_allocation_repository;
    protected $site_note_repository;

    /**
     *
     */
    public function __construct(
        CustomerEmployeeAllocationRepository $customer_employee_allocation_repository,
        SiteNoteRepository $site_note_repository
    )
    {
        $this->customer_employee_allocation_repository = $customer_employee_allocation_repository;
        $this->site_note_repository = $site_note_repository;
    }

    /**
     * Authorization
     *
     * @return boolean
     */
    public function authorize()
    {
        $request_customer = request('customer_id');
        $request_note = request('note_id');
        $allocated_customers = $this->customer_employee_allocation_repository->getAllocatedCustomers(\Auth::user());
        $site_notes_from_customer = $this->site_note_repository->getSiteNoteByCustomer($request_customer)->pluck('id')->toArray();
        if(
            in_array($request_customer, $allocated_customers)
            &&
            ($request_note == 0) || ($request_note > 0 && in_array($request_note, $site_notes_from_customer))
        ){
            return true;
        } else{
            return false;
        }        
    }    

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('customer_id');
        $custom_subject_rule = [];
        $custom_rules = [];
        $subject_id = request('subject');
        $task_list = request('task_list');
        $task_count = count(request('task_list'));
        $rules = [           
            'subject' => 'bail|required|max:50',            
            'attendees' => 'bail|required|max:100',
            'location' => 'bail|required|max:100',
            'notes' => 'bail|required|min:1|max:20000',
        ];

        for ($i = 0; $i < $task_count; $i++) {
            $task_rules = [
                'task_list.'. $i .'.task_subject'  => ['bail', 
                                                        'required_with:task_list.'. $i .'.due_date' , 
                                                        'required_with:task_list.'. $i .'.task_status', 
                                                        'required_with:task_list.'. $i .'.task_subject', 
                                                        'required_with:task_list.'. $i .'.user',
                                                        'max:1000'],
                'task_list.'. $i .'.task_status' => ['bail', 
                                                    'not_in:Select',
                                                    'required_with:task_list.'. $i .'.task_subject', 
                                                    'nullable', ],
                'task_list.'. $i .'.due_date' => ['bail', 
                                                    'required_with:task_list.'. $i .'.task_subject', 
                                                    'nullable', 
                                                    'date_format:"Y-m-d"', ], 
                'task_list.'. $i .'.assignee' => ['bail', 
                                                    'not_in:Select',
                                                    'required_with:task_list.'. $i .'.task_subject', 
                                                    'nullable', 
                                                    'max:30'],   
            ];
            $rules = array_merge($rules, $task_rules);
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
            'subject.max' => 'Subject may not be greater than 50 characters.',
            'attendees.max' => 'Subject may not be greater than 100 characters.',
            'location.max' => 'Subject may not be greater than 100 characters.',
            'notes.max' => 'Subject may not be greater than 20000 characters.',
            'date.required' => 'Date is required.',
            'date.min' => 'Please input valid date',
            'date.max' => 'Please input valid date.',            
            'task_list.*.task_subject.required_with' => 'Subject is required.',
            'task_list.*.task_subject.max' => 'Task subject may not be greater than 1000 characters.',
            'task_list.*.task_status.required_with' => 'Status is required.',
            'task_list.*.due_date.required_with' => 'Due date is required.',
            'task_list.*.due_date.date_format' => 'Please enter the date in Y-m-d format',
            'task_list.*.assignee.required_with' => 'Assignee is required.',
            'task_list.*.assignee.max' => 'Assignee characters should be less than 30.',
            
        ];
    }
}
