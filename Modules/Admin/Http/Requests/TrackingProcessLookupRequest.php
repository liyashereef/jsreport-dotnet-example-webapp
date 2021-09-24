<?php

namespace Modules\Admin\Http\Requests;

class TrackingProcessLookupRequest extends Request
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
            'process_steps' => "bail|required|max:255|unique:tracking_process_lookups,process_steps,{$id},id,deleted_at,NULL",
            'step_number' => "bail|required|numeric|unique:tracking_process_lookups,step_number,{$id},id,deleted_at,NULL",
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
            'process_steps.required' => 'Process Step is required.',
            'process_steps.unique' => 'This Process Step is already added.',
            'step_number.unique' => 'This Step is already added.',
        ];
    }

}
