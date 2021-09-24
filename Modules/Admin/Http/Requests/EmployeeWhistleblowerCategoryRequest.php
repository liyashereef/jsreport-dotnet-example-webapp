<?php

namespace Modules\Admin\Http\Requests;

class EmployeeWhistleblowerCategoryRequest extends Request
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
            'roles' => "bail|required|max:255|unique:employee_whistleblower_categories,roles,{$id},id,deleted_at,NULL",
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
            'roles.required' => 'Category is required.',
            'roles.unique' => 'This Category is already added.',
        ];
    }

}
