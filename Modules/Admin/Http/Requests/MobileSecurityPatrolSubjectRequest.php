<?php

namespace Modules\Admin\Http\Requests;

class MobileSecurityPatrolSubjectRequest extends Request
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
            'subject' => "bail|required|max:255|unique:mobile_security_patrol_subjects,subject,{$id},id,deleted_at,NULL",
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
            'subject.required' => 'Subject is required.',
            'subject.unique' => 'This Subject is already added.',
        ];
    }

}
