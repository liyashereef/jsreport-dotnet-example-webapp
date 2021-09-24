<?php

namespace Modules\Admin\Http\Requests;

class ScheduleAssignmentTypeRequest extends Request
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
            'type' => "bail|required|max:255|unique:schedule_assignment_type_lookups,type,{$id},id,deleted_at,NULL",
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
            'type.required' => 'Schedule Assignment Type is required.',
            'type.unique' => 'This Schedule Assignment Type is already added.',
            'type.max' => 'The Schedule Assignment Type should not exceed 255 characters.',
        ];
    }

}
