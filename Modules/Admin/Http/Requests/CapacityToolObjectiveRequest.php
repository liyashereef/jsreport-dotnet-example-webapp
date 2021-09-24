<?php

namespace Modules\Admin\Http\Requests;

class CapacityToolObjectiveRequest extends Request
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
            'value' => "bail|required|max:191|unique:capacity_tool_objective_lookups,value,{$id},id,deleted_at,NULL",
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
            'value.required' => 'Objective is required.',
            'value.unique' => 'This objective is already added.',
            'value.max' => 'The objective should not exceed 190 characters.',
        ];
    }

}
