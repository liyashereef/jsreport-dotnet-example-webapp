<?php

namespace Modules\Admin\Http\Requests;

class StcThresholdSettingRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [
            'no_of_days_critical' => 'required|numeric|min:0',
            'critical_days_color' => 'required',
            'no_of_days_major' => 'required|numeric|min:0',
            'major_days_color' => 'required',
            'no_of_days_minor' => 'required|numeric|min:0',
            'minor_days_color' => 'required',
        ];

        return $rules;
    }

}
