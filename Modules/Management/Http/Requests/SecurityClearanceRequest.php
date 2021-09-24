<?php

namespace Modules\Management\Http\Requests;
use Modules\Admin\Http\Requests\UserRequest;


class SecurityClearanceRequest extends UserRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $row_ids = request('row-no');
       
        $rules = [];
        
        if ($row_ids != null) {
            foreach ($row_ids as $id) {
                $security_clearance_rules = [
                    'security_clearance_' . $id => 'bail|nullable',
                    'valid_until_' . $id => 'bail|required_with:security_clearance_' . $id . '|nullable|date',
                ];
                $rules = array_merge($rules, $security_clearance_rules);
            }
        }
                
            return $rules;
    }
    public function messages()
    {
        $id = request('id');
        $row_ids = request('row-no');
        $message = [];

        if ($row_ids != null) {
            foreach ($row_ids as $id) {
                $security_clearance_rules = [
                    'security_clearance_' . $id . '.not_in' => 'Please choose a security clearance.',
                    'valid_until_' . $id . '.date' => 'Enter a valid date.',
                    'valid_until_' . $id . '.required_with' => 'Enter valid until date.',
                ];
                $message = array_merge($message, $security_clearance_rules);
            }
        }
        
        return $message;
    }
}