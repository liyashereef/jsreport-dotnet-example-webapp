<?php

namespace Modules\Admin\Http\Requests;

class SmartPhoneTypeRequest extends Request
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
            'type' => "bail|required|max:255|unique:smart_phone_types,type,{$id},id,deleted_at,NULL",
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
            'type.required' => 'Type is required.',
            'type.unique' => 'This Type is already added.',
            'type.max' => 'The Type should not exceed 255 characters.',
        ];
    }

}
