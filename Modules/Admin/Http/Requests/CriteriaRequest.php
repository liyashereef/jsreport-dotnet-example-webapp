<?php

namespace Modules\Admin\Http\Requests;

class CriteriaRequest extends Request
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
            'criteria' => "bail|required|max:255|unique:criteria_lookups,criteria,{$id},id,deleted_at,NULL",
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
            'criteria.required' => 'Criteria is required.',
            'criteria.unique' => 'This Criteria is already added.',
            'criteria.max' => 'This Criteria should not exceed 255 characters.',
        ];
    }

}
