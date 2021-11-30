<?php

namespace Modules\Admin\Http\Requests;

class TimeOffCategoryRequest extends Request
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
            'type' => "bail|required|max:150|unique:timeoff_category_lookup,type,{$id},id,deleted_at,NULL",
            'description' => "bail|required|max:1000",
            'reference' => "bail|required|max:150",
            'allowed_days' => "bail|required|numeric|digits_between:1,5",
            'allowed_hours' => "bail|required|numeric|digits_between:1,8",
            'allowed_weeks' => "bail|required|numeric|digits_between:1,5",
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
            'type.required' => 'Category Type is required.',
            'type.unique' => 'Category Type is already added.',
            'type.max' => 'Category Type should not exceed 150 characters.',
            'allowed_days.digits_between' => 'The allowed days must be maximum 5 digits',
            'allowed_hours.digits_between' => 'The allowed hours must be maximum 8 digits',
            'allowed_weeks.digits_between' => 'The allowed weeks must be maximum 5 digits',


        ];
    }

}
