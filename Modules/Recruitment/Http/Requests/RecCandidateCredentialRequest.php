<?php

namespace Modules\Recruitment\Http\Requests;

class RecCandidateCredentialRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');

        $rules = [
            'first_name' => 'bail|required|regex:/^[0-9A-Za-z\'\s\-]+$/u|max:255' . $id,
            'last_name' => 'bail|required|regex:/^[0-9A-Za-z\'\s\-]+$/u|max:255',
            //'city' => 'bail|nullable|max:255',
            //'postalCode' => 'bail|nullable|regex:/^[a-zA-Z][0-9][a-zA-Z][0-9][a-zA-Z][0-9]$/|max:6|min:6',
            //'phone' => 'bail|nullable|max:13|min:13',
            'email' => "bail|required|max:255|email|unique:mysql_rec.rec_candidates,email," . $id . ",id,deleted_at,NULL",
            'username' => "bail|required|max:255|unique:mysql_rec.rec_candidates,username," . $id . ",id,deleted_at,NULL",
            //'password' => 'required_if:id,""|nullable|min:8',
        ];

        return $rules;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function messages()
    {
        $message = [
            'first_name.required' => 'Name is required.',
            'first_name.regex' => 'Please enter only characters.',
            'last_name.required' => 'Name is required.',
            'last_name.regex' => 'Please enter only characters.',
            //'city.required' => 'Please enter city',
            //'postal_code.max' => 'Please enter postal code with 6 characters',
            //'postal_code.min' => 'Please enter postal code with 6 characters',
            //'postal_code.required' => 'Please enter postal code with 6 characters',
            //'phone.required' => 'Please enter phone',
            'email.required' => 'Please enter email',
            'username.required' => 'User name is required.',
            // 'password.required_if' => 'Password is required.',
            // 'password.min' => 'Password with minimum 8 character is required.',
        ];

        return $message;
    }
}
