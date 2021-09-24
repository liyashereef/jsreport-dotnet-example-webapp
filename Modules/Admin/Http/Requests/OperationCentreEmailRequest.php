<?php

namespace Modules\Admin\Http\Requests;

class OperationCentreEmailRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        return $rules = [
            'email' => "bail|required|email|max:255|unique:operations_centre_email,email,{$id},id,deleted_at,NULL",
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
            'email.required' => 'Email is required',
            // 'email.required' => 'Pay period name is required.',
            // 'email.unique' => 'This Pay period is already added.',
            // 'email.required' => 'Start Date is required.',
            // 'email.required' => 'End Date is required.',
        ];
    }

}
