<?php

namespace Modules\Admin\Http\Requests;

class ShiftModuleDropdownRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $info_id = request('info_id');

        return [
            'dropdown_name' => 'bail|required|max:100|unique:shift_module_dropdowns,dropdown_name,' . $id . ',id,deleted_at,NULL',
            'option_name.*' => 'bail|required|distinct',
            'option_info.*' => 'bail|max:1000|required_if:info_id,==,1',
            'order_sequence.*' => 'bail|required|integer|min:1|max:1000|distinct',

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
            'dropdown_name.required' => 'Dropdown name is required',

            'option_name.*.required' => 'Option is required',
            'order_sequence.*.required' => 'Order Sequence is required',
            'order_sequence.*.integer' => 'Order Sequence must be an integer',
            'option_name.*.distinct' => 'Option already exists',
            'option_info.*.required_if' => 'Information is required',
            'option_info.*.max' => 'Maximum character length must be less than 1000',
            'order_sequence.*.min' => 'Please choose number greater than 0',
            'order_sequence.*.max' => 'Please choose number less than 1000',
            'order_sequence.*.distinct' => 'Order sequence already exists',

        ];
    }
}
