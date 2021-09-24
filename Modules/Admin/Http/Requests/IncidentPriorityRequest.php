<?php

namespace Modules\Admin\Http\Requests;

class IncidentPriorityRequest extends Request
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
            'value' => "bail|required|max:191|unique:incident_priority_lookups,value,{$id},id,deleted_at,NULL",
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
            'value.required' => 'Incident Priority is required.',
            'value.unique' => 'Incident Priority is already added.',
            'value.max' => 'The Incident Priority should not exceed 190 characters.',
        ];
    }

}
