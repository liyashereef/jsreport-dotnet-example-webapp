<?php

namespace Modules\Admin\Http\Requests;

class UserSkillsRequest extends Request
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
            'name' => "bail|required|unique:user_skills,name,{$id},id,deleted_at,NULL",
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
            'name.required' => 'Skill is required.',
            'name.unique' => 'This Skill is already added.',
        ];
    }
}
