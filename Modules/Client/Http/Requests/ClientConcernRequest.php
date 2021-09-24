<?php

namespace Modules\Client\Http\Requests;

class ClientConcernRequest extends Request
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
            'severity' => 'bail|required|not_in:0',
            'concern' => 'bail|required|max:5000',

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
            'severity.required' => 'Severity Level is required.',
            'status_lookup_id.not_in' => 'Severity Level is required.',
            'concern.required' => 'Concern is required.',
            'concern.max' => 'Concern should not exceed 5000 characters.',
            'reg_manager_notes.required' => 'Regional manager notes field is required.' ,
            'reg_manager_notes.max' => 'Please enter the notes within 1000 characters.',
            'status_lookup_id.required' => 'Please choose the status.',
            'status_lookup_id.not_in' => 'Please choose the status.',
        ];
    }

}
