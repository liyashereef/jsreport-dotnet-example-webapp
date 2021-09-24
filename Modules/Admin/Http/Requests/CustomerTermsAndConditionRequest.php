<?php

namespace Modules\Admin\Http\Requests;

class CustomerTermsAndConditionRequest extends Request
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
            'customer_id' => "required",
            'terms_and_conditions' => "required",
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
            'customer_id.required' => 'Customer is required.',
            'terms_and_conditions.required' => 'Terms and conditions is required.',
        ];
    }

}
