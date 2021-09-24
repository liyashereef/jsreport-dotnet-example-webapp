<?php

namespace Modules\Admin\Http\Requests;

class CustomerIncidentPriorityRequest extends Request
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
            'response_time.*' =>  "bail|required|numeric|digits_between:1,50",
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
            'response_time.*.required' => 'Response Time is required.',
        ];
    }

}
