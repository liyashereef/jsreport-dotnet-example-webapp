<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailAccountsRequest extends FormRequest
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
            'display_name' => "bail|required|max:255|",
            'user_name' => "bail|required|max:255|unique:email_accounts_masters,user_name," . $id . ",id,deleted_at,NULL",
            'email_address' => "bail|required|max:255|email|unique:email_accounts_masters,email_address," . $id . ",id,deleted_at,NULL",
            'password' => 'bail|required|nullable|min:8,',
            'smtp_server' =>  "bail|required",
            'port' =>  "bail|required",
            'encryption' =>  "bail|required",
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function messages()
    {
        return [
            'display_name.required' => 'Display name is required.',
            'display_name.max' =>  'Display name cannot exceed 255 characters',
            'user_name.required' => 'User name is required.',
            'user_name.unique' => 'Unique user name is required.',
            'email_address.required' => 'Email address is required.',
            'email_address.unique' => 'Unique Email is required.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password with minimum 8 character is required.',
            'smtp_server.required' => 'SMTP Server is required.',
            'port.required' => 'Port is required.',
            'encryption.required' => 'Encryption is required.',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
