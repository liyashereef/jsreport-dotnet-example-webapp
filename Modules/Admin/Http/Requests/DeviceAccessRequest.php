<?php

namespace Modules\Admin\Http\Requests;

class DeviceAccessRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $devicetitle = request('devicetitle');
        return $rules = [
            'devicetitle' => "required",       
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
            'devicetitle.required' => 'Device Access is required.',
        ];
    }

}
