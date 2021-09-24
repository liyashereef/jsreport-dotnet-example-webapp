<?php

namespace Modules\Admin\Http\Requests;

class UserSalutationRequest extends Request
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
            'salutation' => "bail|required|regex:/^[a-zA-Z0-9 .\-\)\(\[\]]*$/|max:20|unique:user_salutations,salutation,{$id},id,deleted_at,NULL",
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function messages()
    {
        return [
            'salutation.required' => 'Salutation is required.',
            'salutation.unique' => 'This Salutation is already added.',
            'salutation.max' => 'The Salutation should not exceed 20 characters.',
            
        ];
    }
}
