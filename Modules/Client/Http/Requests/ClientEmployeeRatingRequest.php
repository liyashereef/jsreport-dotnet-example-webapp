<?php

namespace Modules\Client\Http\Requests;

class ClientEmployeeRatingRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $review_permission = request('review_permission');

        $rules = [
            'employee_rating_lookup_id' => 'bail|required|not_in:0',
            'customer_feedback' => 'bail|required|max:1000',
        ];

        if ($review_permission == 0) {
            $other_rules = [
                'reg_manager_notes' =>  'bail|required|max:1000',
                'status_lookup_id' => 'bail|required|not_in:0',
            ];
            $rules = array_merge($rules, $other_rules);
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
            'customer_feedback.required' => 'Feedback is required.',
            'customer_feedback.max' => 'Feedback should not exceed 1000 characters.',
            'employee_rating_lookup_id.required' => 'Please choose the rating.',
            'employee_rating_lookup_id.not_in' => 'Please choose the rating.',
            'reg_manager_notes.required' => 'Regional manager notes field is required.' ,
            'reg_manager_notes.max' => 'Please enter the notes within 1000 characters.',
            'status_lookup_id.required' => 'Please choose the status.',
            'status_lookup_id.not_in' => 'Please choose the status.',
        ];
    }

}
