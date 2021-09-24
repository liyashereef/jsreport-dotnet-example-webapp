<?php

namespace Modules\Facility\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FacilityUserUpdatePasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'current_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'current_password.required' => 'Password is mandatory.',      
            'new_password.required' => 'New password is mandatory.',      
            'confirm_password.required' => 'Confirm password is mandatory.',      
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
