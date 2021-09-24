<?php

namespace Modules\Admin\Http\Requests;

class LeaveReasonRequest extends Request
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
            'reason' => "bail|required|max:255|unique:leave_reasons,reason,{$id},id,deleted_at,NULL",
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
            'reason.required' => 'Leave Reason is required.',
            'reason.unique' => 'This Leave Reason is already added.',
            'reason.max' => 'The Leave Reason should not exceed 255 characters.',
        ];
    }

}
