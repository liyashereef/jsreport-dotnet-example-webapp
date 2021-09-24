<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KeyMangementMobileAppSettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'keymanagement_module_image_limit' => 'bail|required|numeric|min:1|max:10',
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
            'keymanagement_module_image_limit.required' => 'Key management image limit is required.',
            'keymanagement_module_image_limit.numeric' => 'Only numeric values are accepted.',
            'keymanagement_module_image_limit.min' => 'Minimum  value should be 1.',
            'keymanagement_module_image_limit.max' => 'You cannot exceed 10 images.'
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
