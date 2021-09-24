<?php

namespace Modules\Admin\Http\Requests;

class CustomerUseridMappingRequest extends Request
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
            'type' => "bail|required|not_in:0",
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
            'type.required' => 'Type is required.',
            'type.not_in'=>'Please choose type'
        ];
    }

}
