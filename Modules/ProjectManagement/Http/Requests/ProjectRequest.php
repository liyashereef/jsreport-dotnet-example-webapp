<?php

namespace Modules\ProjectManagement\Http\Requests;


class ProjectRequest extends Request
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
            'name' => "required|max:100",
            'customer_id'=>"required"       
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
        ];
    }

}