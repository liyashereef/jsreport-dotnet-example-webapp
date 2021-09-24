<?php

namespace Modules\Admin\Http\Requests;

class CapacityToolSkillTypeRequest extends Request
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
            'value' => "bail|required|max:191|unique:capacity_tool_skill_type_lookups,value,{$id},id,deleted_at,NULL",
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
            'value.required' => 'Skill Type is required.',
            'value.unique' => 'This skill type is already added.',
            'value.max' => 'The skill type should not exceed 190 characters.',
        ];
    }

}
