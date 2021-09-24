<?php

namespace Modules\ProjectManagement\Http\Requests;

class TaskRequest extends Request
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
            'name' => "required",
            'assigned_to'=>"required|not_in:0",
            'due_date'=>"required|date|after:today"
     
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
            'name.required' => 'Project name is required.',
            'assigned_to.not_in'=>'Please choose assignee'
        ];
    }
}
