<?php

namespace Modules\Admin\Http\Requests;

class RolesPermissionRequest extends Request
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
            'role' => "bail|required|unique:roles,name,{$id},id|max:50",
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
            'role.required' => 'Role is required.',
            'role.max' => 'Role name should not exceed 50 characters.',
        ];
    }

}
