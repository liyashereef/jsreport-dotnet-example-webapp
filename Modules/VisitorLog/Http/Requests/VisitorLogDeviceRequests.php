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
        $rules = [
            'pin' => "required|numeric|digits:5",
            'template_id' => "required",
            'name' => "required",
            'camera_mode' => "required",
            'scaner_camera_mode' => "required"
        ];

        //On edit mode
        $customerRules = [];
        $id = request('id');
        if (empty($id)) {
            $customerRules = [
                'customer_id' => "required",
            ];
        }
        $rules = array_merge($rules, $customerRules);
        return $rules;
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
