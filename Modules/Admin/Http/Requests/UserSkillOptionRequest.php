<?php

namespace Modules\Admin\Http\Requests;

class UserSkillOptionRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        return [
            'option_name' => 'bail|required|max:100|unique:user_skill_options,name,' . $id . ',id,deleted_at,NULL',
            'skill_id.*' => 'bail|required|min:1',
            'skill_id' => 'bail|required|array|min:1',
            'option_value.*' => 'bail|required|max:1000',
            'order.*' => 'bail|required|integer|min:1|max:1000|distinct',

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
            'option_name.required' => 'Option name is required',
            'skill_id.*.required' => 'Please choose skill',
            'order.*.required' => 'Order Sequence must be an integer',
            'option_value.*.required' => 'Option value is required',
            'option_value.*.max' => 'Maximum character length must be less than 1000',
            'order.*.min' => 'Please choose number greater than 0',
            'order.*.max' => 'Please choose number less than 1000',
            'order.*.distinct' => 'Order sequence already exists',
            'order.*.integer'=>'Order Sequence must be an integer'

        ];
    }
}
