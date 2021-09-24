<?php

namespace Modules\Admin\Http\Requests;

class CompliancePolicyCategoryRequest extends Request
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
            'compliance_policy_category' => "bail|required|max:255|unique:compliance_policy_categories,compliance_policy_category,{$id},id,deleted_at,NULL",
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
            'compliance_policy_category.required' => 'Course Category is required.',
            'compliance_policy_category.unique' => 'This Course Category is already added.',
            'compliance_policy_category.max' => 'The Course Category should not exceed 255 characters.',
        ];
    }

}
