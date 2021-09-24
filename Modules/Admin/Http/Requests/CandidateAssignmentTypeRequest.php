<?php

namespace Modules\Admin\Http\Requests;

class CandidateAssignmentTypeRequest extends Request
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
            'type' => "bail|required|max:255|unique:assignment_types_lookups,type,{$id},id,deleted_at,NULL",
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
            'type.required' => 'Assignment Type is required.',
            'type.unique' => 'This Assignment Type is already added.',
            'type.max'=>'The assignment type should not exceed 255 characters.'
        ];
    }

}
