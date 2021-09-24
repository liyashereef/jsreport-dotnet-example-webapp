<?php

namespace Modules\Admin\Http\Requests;

class ClientEmailTemplateRequest extends Request
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
            'type_id' => "bail|required|not_in:0",
            'email_subject'=> "bail|required|max:190",
            'editors'=> "bail|required",
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
            'type_id.required' => 'Type is required.',
            'type_id.not_in' =>'Please choose any one',
            'email_subject.required' => 'Subject is required',
            'value.max' => 'The task frequency should not exceed 190 characters.',
            'editors.required'=>'Email body is required'
        ];
    }

}
