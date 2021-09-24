<?php

namespace Modules\Recruitment\Http\Requests;


class RecProcessStepsRequest extends Request
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
            'display_name' => "bail|required|max:255|unique:mysql_rec.rec_process_steps,display_name,{$id},id,deleted_at,NULL",
            'step_order' => "bail|required|numeric|unique:mysql_rec.rec_process_steps,step_order,{$id},id,deleted_at,NULL",
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
            'display_name.required' => 'Process Step is required.',
            'display_name.unique' => 'This Process Step is already added.',
            'step_order.unique' => 'This Step is already added.',
        ];
    }

}
