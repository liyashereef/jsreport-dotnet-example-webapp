<?php

namespace Modules\Admin\Http\Requests;

class QrPatrolSettingRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [
            'days_prior' => 'required|numeric|max:31|min:7',
            'critical_level_percentage' => 'required|numeric|max:100|min:0',
            'acceptable_level_percentage' => 'required|numeric|max:100|min:0',
        ];

        return $rules;
    }

}
