<?php

namespace Modules\Admin\Http\Requests;

class CapacityToolWorkClassificationAreaRequest extends Request
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
            'value' => "bail|required|max:191|unique:capacity_tool_work_classification_area_lookups,value,{$id},id,deleted_at,NULL",
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
            'value.required' => 'Area is required.',
            'value.unique' => 'This area is already added.',
            'value.max' => 'The area should not exceed 190 characters.',
        ];
    }

}
