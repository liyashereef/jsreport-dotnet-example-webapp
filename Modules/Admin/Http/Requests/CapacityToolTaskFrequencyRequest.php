<?php

namespace Modules\Admin\Http\Requests;

class CapacityToolTaskFrequencyRequest extends Request
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
            'value' => "bail|required|max:191|unique:capacity_tool_task_frequency_lookups,value,{$id},id,deleted_at,NULL",
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
            'value.required' => 'Task Frequency is required.',
            'value.unique' => 'This task frequency is already added.',
            'value.max' => 'The task frequency should not exceed 190 characters.',
        ];
    }

}
