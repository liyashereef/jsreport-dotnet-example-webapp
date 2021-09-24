<?php

namespace Modules\Compliance\Http\Requests;

class PolicyAcceptanceRequest extends Request
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
            'compliance_policy_agree_reason_id' => "sometimes|required",
            'comment' => "sometimes|required|max:1000",
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
            'compliance_policy_agree_reason_id.required' => 'Please choose a reason',
            'comment.unique' => 'This status is already added.',
        ];
    }

}
