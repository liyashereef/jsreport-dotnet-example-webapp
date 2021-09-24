<?php

namespace Modules\Admin\Http\Requests;

class CompliancePolicyRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $rules = [
            'policy_name' => "bail|required|max:255|unique:compliance_policies,policy_name,{$id},id,deleted_at,NULL",
            'compliance_policy_category_id' => "bail|required",
            'compliance_policy_roles' => 'bail|required',
            'policy_description' => "bail|required|max:1000",
            'policy_objectives' => "bail|required|max:1000",
            'agree_reasons' => 'bail|nullable|required_with:enable_agree_or_disagree',
            'disagree_reasons' => 'bail|nullable|required_with:enable_agree_or_disagree',

        ];
        if ($id == null) {
            $other_rules = [
                'policy_file' => "bail|required|mimes:pdf|max:100000",
            ];
            $rules = array_merge($rules, $other_rules);
        }
        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'policy_name.required' => 'Policy name is required.',
            'policy_name.unique' => 'This Policy name is already taken.',
            'policy_name.max' => 'The Policy name should not exceed 255 characters.',
            'compliance_policy_category_id.required' => 'Category is required.',
            'policy_description.required' => 'Policy description is required.',
            'policy_objectives.required' => 'Policy objective is required.',
            'policy_file.required' => 'Policy file is required.',
            'policy_file.mimes' => 'Policy file should be in PDF format.',
            'agree_reasons.required_with' => 'Please provide atleast one option',
            'disagree_reasons.required_with' => 'Please provide atleast one option',
        ];
    }

}
