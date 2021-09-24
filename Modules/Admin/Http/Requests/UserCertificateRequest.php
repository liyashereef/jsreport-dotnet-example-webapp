<?php

namespace Modules\Admin\Http\Requests;

class UserCertificateRequest extends Request
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
            'certificate_name' => "bail|required|max:255|unique:certificate_masters,certificate_name,{$id},id,deleted_at,NULL",
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
            'certificate_name.required' => 'Certificate Name is required.',
            'certificate_name.unique' => 'This Certificate Name is already added.',
            'certificate_name.max' => 'The Certificate Name should not exceed 255 characters.',
        ];
    }

}
