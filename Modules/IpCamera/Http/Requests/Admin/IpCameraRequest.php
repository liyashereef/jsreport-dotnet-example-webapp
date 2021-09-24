<?php

namespace Modules\IpCamera\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class IpCameraRequest extends FormRequest
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
            'name' => "bail|required|max:200|unique:ip_cameras,name,{$id},id,deleted_at,NULL",
            'credential_username' => "bail|required|max:200",
            'credential_password' => "bail|required|max:200",
            'rtsp_port'=> "bail|required|max:200",
            'ip' => "bail|required|max:300",
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
            'name.required' => 'IP Camera name is required.',
            'name.unique' => 'IP Camera name is already added.',
            'name.max' => 'IP Camera name should not exceed 200 characters.',
            'credential_username.required' => 'Username is required.',
            'credential_username.max' => 'The Username field should not exceed 200 characters.',
            'credential_password.required' => 'Password is required.',
            'credential_password.max' => 'The Password field should not exceed 200 characters.',
            'ip.required' => 'IP  is required.',
            'ip.max' => 'The IP should not exceed 300 characters.',
            'rtsp_port.required' => 'RTSP Port  is required.',
            'rtsp_port.max' => 'The RTSP Port should not exceed 300 characters.',
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
