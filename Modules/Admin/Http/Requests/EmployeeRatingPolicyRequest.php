<?php

namespace Modules\Admin\Http\Requests;

class EmployeeRatingPolicyRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        return $rules = [
            'policy' => "required|max:255",
            'description' => "max:1000",
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'policy.required' => 'Policy is required.',
            
            'policy.max' => 'Policy should not exceed 255 characters.',
            'description.max' => 'Policy description should not exceed 1000 characters.',

        ];
    }

}
