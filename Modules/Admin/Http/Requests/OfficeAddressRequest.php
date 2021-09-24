<?php

namespace Modules\Admin\Http\Requests;

class OfficeAddressRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $ratechangetitile = request();
        return $rules = [
            'officeaddresstitle' => "required",
            'officeaddress' => "required",       
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
            'officeaddresstitle.required' => 'Title for address is required.',
            'officeaddresstitle.required' => 'Brief address is required.',
        ];
    }

}
