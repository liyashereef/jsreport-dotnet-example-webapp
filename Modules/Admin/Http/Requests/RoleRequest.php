<?php

namespace Modules\Admin\Http\Requests;

class RoleRequest extends Request
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
            'value' => "bail|required|max:191|unique:role_lookups,value,{$id},id,deleted_at,NULL",
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
            'value.required' => 'Role name is required.',
            'value.unique' => 'This role name is already added.',
            'value.max' => 'The role name should not exceed 190 characters.',
        ];
    }

}
