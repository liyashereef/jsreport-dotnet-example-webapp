<?php

namespace Modules\Admin\Http\Requests;

class WhistleblowerMasterRequest extends Request
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
            'name' => "bail|required|max:50",
            'status' => "bail|required",
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
            'name.required' => 'Name is required.',
            'name.max' => 'The Name should not exceed 50 characters.',
            'status.required'=>'Status is required'
        ];
    }
}
