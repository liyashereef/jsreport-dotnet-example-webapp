<?php

namespace Modules\Admin\Http\Requests;

class MobileAppSettingRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [
            'time_interval' => 'required|numeric',
            'speed_limit' => 'required|numeric',
            'trip_show_speed' => 'nullable|numeric',
            'shift_module_image_limit' => 'numeric|min:0|max:10',
            'key_management_module_image_limit' => 'numeric|min:1|max:10',
        ];

        return $rules;
    }

}
