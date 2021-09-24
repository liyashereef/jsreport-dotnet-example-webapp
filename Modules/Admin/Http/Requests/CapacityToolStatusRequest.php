<?php

namespace Modules\Admin\Http\Requests;

class CapacityToolStatusRequest extends Request
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
            'value' => "bail|required|max:1000|unique:capacity_tool_status_lookups,value,{$id},id,deleted_at,NULL",
            'short_name' => "bail|required|max:300|unique:capacity_tool_status_lookups,short_name,{$id},id,deleted_at,NULL",
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
            'value.required' => 'Status is required.',
            'value.unique' => 'This status is already added.',
            'value.max' => 'The status should not exceed 1000 characters.',
            'short_name.required' => 'Short name is required.',
            'short_name.unique' => 'This short name is already added.',
            'short_name.max' => 'The short name should not exceed 300 characters.',
        ];
    }

}
