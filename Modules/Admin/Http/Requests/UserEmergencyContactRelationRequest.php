<?php

namespace Modules\Admin\Http\Requests;

class UserEmergencyContactRelationRequest extends Request
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
            'relations' => "bail|required|regex:/^[a-zA-Z0-9 .\-\)\(\[\]]*$/|max:255|unique:user_emergency_contact_relations,relations,{$id},id,deleted_at,NULL",
            'apogee_code' => "bail|required|regex:/^[a-zA-Z0-9 .\-\)\(\[\]]*$/|max:255|unique:user_emergency_contact_relations,apogee_code,{$id},id,deleted_at,NULL",
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
            'relations.required' => 'Relation is required.',
            'relations.unique' => 'This Relation is already added.',
            'relations.max' => 'The Relation should not exceed 255 characters.',
            'apogee_code.required'=>'Apogee code is required'
        ];
    }
}
