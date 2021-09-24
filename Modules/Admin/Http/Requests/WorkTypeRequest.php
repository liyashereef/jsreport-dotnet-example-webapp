<?php

namespace Modules\Admin\Http\Requests;

class WorkTypeRequest extends Request
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
            'type' => 'bail|required|max:255|unique:work_types,type,' . $id . ',id,active,1',
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
            'type.required' => 'Activity type is required.',
            'type.unique' => 'Activity type already exist.',
        ];
    }
}
