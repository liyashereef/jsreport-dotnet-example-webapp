<?php

namespace Modules\Osgc\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OsgcChangePasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            
            'password' => 'required:password|min:8|confirmed|max:14',
            'password_confirmation' => 'required',
            'old_password' => 'required',
            
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
