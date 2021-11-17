<?php

namespace Modules\VisitorLog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VisitorLogDeviceRequests extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer_id' => "required",
            'name' => "required",
            'camera_mode' => "required",
            'scaner_camera_mode' => "required"
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
            'customer_id.required' => 'Customer is required.',
            'name.required' => 'Name is required.',
            'camera_mode.required' => 'Camera mode is required.',
            'scaner_camera_mode.required' => 'Scaner camera mode is required.',
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
