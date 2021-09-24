<?php

namespace Modules\Admin\Http\Requests;


class RfpTrackingProcessStepLookupRequest extends Request
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
            'process_steps' => "bail|required|max:255|unique:rfp_process_steps,process_steps,{$id},id,deleted_at,NULL",
            'step_number' => "bail|required|numeric|unique:rfp_process_steps,step_number,{$id},id,deleted_at,NULL",
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function messages()
    {
        return [
            'process_steps.required' => 'Process Step is required.',
            'process_steps.unique' => 'This Process Step is already added.',
            'step_number.unique' => 'This Step is already added.',
        ];
    }
}
