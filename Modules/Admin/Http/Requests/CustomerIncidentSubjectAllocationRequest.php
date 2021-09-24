<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Validation\Rule;

class CustomerIncidentSubjectAllocationRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $customer_id = request('customer_id');
        $subject_id = request('subject_id');
         $rules = [
            'category_id' =>  "bail|required",
            'incident_response_time' =>  "bail|required|integer|min:1|max:24",
            'sop' =>  "bail|required",

        ];

        $subject_rules = ['subject_id' => ['required',
            Rule::unique('customer_incident_subject_allocations')->where(function ($query) use ($customer_id,$id) {
                $query->where('customer_id', $customer_id);
                $query->whereNull('deleted_at');
                if (!empty($id)) {
                    $query->where('id', '!=', $id);
                }
            })
        ]];

        $rules = array_merge($rules, $subject_rules);
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
            'subject_id.required' => 'Please select subject.',
            'category_id.required' => 'Please select category.',
            'incident_response_time.required' => 'Please add response time.',
            'incident_response_time.integer' => 'Response time should be a whole value',
            'incident_response_time.max' => 'Maximum response time should be 24',
            'incident_response_time.min' => 'Minimum response time should be 1',
            'sop.required' => 'Please add SOP.',
            'subject_id.unique' => 'This subject is already added.',
        ];
    }

}
