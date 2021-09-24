<?php

namespace Modules\Management\Http\Requests;
use Modules\Admin\Http\Requests\UserRequest;


class UserTabRequest extends UserRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $rules = [
            'first_name' => 'bail|required|regex:/^[0-9A-Za-z\'\s\-]+$/u|max:255' . $id,
            'last_name' => 'bail|nullable|regex:/^[0-9A-Za-z\'\s\-]+$/u|max:255',
            'password' => 'nullable|min:8,',
            'username' => "bail|required|max:255|unique:users,username," . $id . ",id,deleted_at,NULL",
            'email' => "bail|required|max:255|email|unique:users,email," . $id . ",id,deleted_at,NULL",
            'role' => 'bail|required',
        ];
        return $rules;
    }

    public function messages()
    {

        $message = [
            'first_name.required' => 'Name is required.',
            'first_name.regex' => 'Please enter only characters.',
            'last_name.regex' => 'Please enter only characters.',
            'username.required' => 'Unique user name is required.',
            'email.required' => 'Unique Email is required.',
            'password.min' => 'Password with minimum 8 character is required.',
            'role.required' => 'Role is required.',
        ];
        return $message;
    }
}
