<?php

namespace Modules\Facility\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditUser extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        
        
            $conditionarray = [
            'first_name' => 'required',
            'email' => 'required|email',
            'phoneno' => 'required',];
        
        return $conditionarray;
        
    }

    public function messages()
    {
        return [
            'first_name.required' => 'First name is mandatory',
            'last_name.required' => 'Last name is mandatory',
            'email.required' => 'Email is a mandatory field',
            'phoneno.required' => 'Phone no is a mandatory field',
            'customer.required' => 'Choose a customer',
            'alternate_email.required' => 'Alternate wmail is a mandatory field',
            'username.required_without' => 'Username is mandatory',
            'password.required_without' => 'Password is mandatory',
            

            
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
