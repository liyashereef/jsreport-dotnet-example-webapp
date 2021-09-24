<?php

namespace Modules\Admin\Http\Requests;

class SeverityLookupRequest extends Request
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
            'severity' => "bail|required|max:191|unique:severity_lookups,severity,{$id},id,deleted_at,NULL",
            'value' => "bail|integer|required|unique:severity_lookups,value,{$id},id,deleted_at,NULL",
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
            'severity.required' => 'Severity is required.',
            'severity.unique' => 'This Severity is already added.',
            'severity.max' => 'The Severity should not exceed 190 characters.',
        ];
    }

}
