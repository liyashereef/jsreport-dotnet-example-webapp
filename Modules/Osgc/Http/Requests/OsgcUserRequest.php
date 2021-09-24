<?php

namespace Modules\Osgc\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OsgcUserRequest extends FormRequest
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
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:osgc_users,email,' . $id . ",id,deleted_at,NULL",
            'password' => 'required:password|min:8|confirmed|max:14',
            'password_confirmation' => 'required',
            'is_veteran'=> 'required|in:1,0',
            'indian_status' => 'required',
            'referral' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'is_veteran.required' => 'The Veteran is required.',
            
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
