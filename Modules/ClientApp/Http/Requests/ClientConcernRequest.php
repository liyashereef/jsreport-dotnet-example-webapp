<?php

namespace Modules\ClientApp\Http\Requests;

class ClientConcernRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $rules = [
            'customerId' => 'bail|required|not_in:0',
            'severity' => 'bail|required|not_in:0',
            'info' => 'bail|required|max:1000',
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
            'severity.required' => 'Severity Level is required.',
            'info.required' => 'Concern is required.',
            'info.max' => 'Concern should not exceed 1000 characters.',
        ];
    }

}
