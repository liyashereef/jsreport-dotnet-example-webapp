<?php

namespace Modules\Admin\Http\Requests;

class ShiftModuleRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        $id = request('id');
        $row_count = request('row_count');
        $rules = [
            'module_name' => "bail|required|max:20",
            'customer_id' => "bail|required|not_in:0",
            'enable_timeshift' => "bail|required",
            'field_order.*' => "bail|required|distinct|min:1|max:100",
            'field_name.*' => "bail|required|distinct|max:200",
            'field_type.*' => "bail|required|not_in:0",

        ];
        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        $row_count = request('row_count');
        $custom_field_messages = [];
        $message = [
            'module_name.required' => 'Module Name is required.',
            'module_name.unique' => 'This Module Name is already added.',
            'enable_timeshift.required' => 'Please choose any one.',
            'customer_id.not_in' => 'Please choose any customer',
            'field_order.*.required' => 'Field order is required',
            'field_name.*.required' => 'Field name is required',
            'field_type.*.required' => 'Field type is required',
            'field_type.*.not_in' => 'Please choose any field type',
            'field_order.*.distinct' => 'Order already exists',
            'field_order.*.min' => 'Please choose number greater than 0',
            'field_order.*.max' => 'Please choose number less than 100',
            'field_name.*.distinct' => 'Field name  already exists',
            'field_name.*.max' => 'Field name should not be greater than 200 characters.',


        ];
        return $message;
    }

}
