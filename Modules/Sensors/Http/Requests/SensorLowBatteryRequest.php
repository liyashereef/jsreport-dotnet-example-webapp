<?php

namespace Modules\Sensors\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class SensorLowBatteryRequest extends SensorEventRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return parent::rules() + [
                'lowBattery' => 'required',
            ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    public function messages()
    {
        return parent::messages() + [
                'lowBattery.required' => 'Battery status required',
            ];
    }
}
