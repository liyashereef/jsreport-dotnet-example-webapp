<?php

namespace Modules\Sensors\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SensorActiveSettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        return [
            'customer_id' => "sometimes|required",
            'room_id' => "sometimes|required",
            'week_day_start_time' => "required",
            'week_day_end_time' => "required",
            'week_end_start_time' => "required",
            'week_end_end_time' => "required",
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function messages()
    {
        return [
            'customer_id.required' => 'Customer is required.',
            'room_id.required' => 'Room is required.',
            'week_day_start_time.required' => 'Week day start time required.',
            'week_day_end_time.required' => 'Week day end time required.',
            'week_end_start_time.required' => 'Week end start time required.',
            'week_end_end_time.required' => 'Week end end time required.',

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
}
