<?php

namespace Modules\Admin\Http\Requests;

class TimeOffRequestTypeRequest extends Request
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
            'request_type' => "bail|required|max:100|unique:time_off_request_type_lookups,request_type,{$id},id,deleted_at,NULL",
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
            'request_type.required' => 'Request Type is required.',
            'request_type.unique' => 'Request Type is already added.',
            'request_type.max' => 'Request Type should not exceed 100 characters.',
        ];
    }

}
