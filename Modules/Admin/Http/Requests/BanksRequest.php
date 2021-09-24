<?php

namespace Modules\Admin\Http\Requests;

class BanksRequest extends Request
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
            'bank_name' => "bail|required|regex:/^[a-zA-Z0-9 .\-\)\(\[\]]*$/|max:255|unique:banks,bank_name,{$id},id,deleted_at,NULL",
            'bank_code' => "bail|required|regex:/^[a-zA-Z0-9 .\-\)\(\[\]]*$/|max:255|unique:banks,bank_code,{$id},id,deleted_at,NULL",
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
            'bank_name.required' => 'Bank Name is required.',
            'bank_name.unique' => 'This Bank Name is already added.',
            'bank_name.max' => 'The Bank Name should not exceed 255 characters.',
            'bank_code.required' => 'Bank Code is required.',
            'bank_code.unique' => 'This Bank Code is already added.',
            'bank_code.max' => 'The Bank Code should not exceed 255 characters.',
        ];
    }
}
