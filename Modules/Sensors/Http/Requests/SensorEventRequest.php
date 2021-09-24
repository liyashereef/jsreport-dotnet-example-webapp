<?php

namespace Modules\Sensors\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class SensorEventRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nodeMacID' => 'required',
            'timestamp' => 'required',
            'topic' => 'required',
            'createdAt' => 'required',
            'sensor' => 'required',
            'sensorConfig' => 'required',
            'room' => 'required',
            'customer' => 'required',
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

    public function withValidator($validator)
    {
        if($validator->messages()->count() > 0) {
            Log::channel('motionSensor')
                ->error("Validation error: "
                    ." {\"commLogID\": \"".request('commLogId')."\","
                    ." \"nodeMacID\": \"".request('nodeMacID')."\","
                    ." \"topic\":  \"".request('topic')."\","
                    ." \"message\": "
                    .$validator->messages()->toJson()
                    ."}");
        }
    }

    public function messages()
    {
        return parent::messages() + [
                'sensor.required' => 'No sensor related to Node Mac found',
                'sensorConfig.required' => 'Config settings not found',
                'room.required' => 'Room - Sensor allocation not found',
                'customer.required' => 'Customer - Room allocation not found',
            ];
    }
}
