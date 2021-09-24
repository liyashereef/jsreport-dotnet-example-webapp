<?php

namespace Modules\Admin\Http\Requests;

class EmployeeWhistleblowerPriorityRequest extends Request
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
            'priority' => "bail|required|max:255|unique:employee_whistleblower_priorities,priority,{$id},id,deleted_at,NULL",
             'rank' => "bail|required|max:999|numeric|unique:employee_whistleblower_priorities,rank,{$id},id,deleted_at,NULL",
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
            'priority.required' => 'Priority is required.',
            'priority.unique' => 'This Priority is already added.',
        ];
    }

}
