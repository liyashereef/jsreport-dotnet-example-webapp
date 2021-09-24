<?php

namespace Modules\Admin\Http\Requests;

class IdsPassportPhotoRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return $rules = [
            'name' => 'required|regex:/^[a-zA-Z0-9 ]+$/u',
            'rate' => 'required'
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
            'name.required' => 'Name is required.',
            'rate.required' => 'Rate is required.',

        ];
    }

}
