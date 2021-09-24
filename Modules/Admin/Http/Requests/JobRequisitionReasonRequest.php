<?php

namespace Modules\Admin\Http\Requests;

class JobRequisitionReasonRequest extends Request
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
            'reason' => "bail|required|max:255|unique:job_requisition_reason_lookups,reason,{$id},id,deleted_at,NULL",
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
            'reason.required' => 'Reason is required.',
            'reason.unique' => 'Reason is already added.',
            'reason.max' => 'Reason should not exceed 255 characters.',
        ];
    }

}
