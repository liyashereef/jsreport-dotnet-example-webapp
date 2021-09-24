<?php

namespace Modules\UniformScheduling\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Admin\Models\UniformSchedulingMeasurementPoints;
class UniformSchedulingEntriesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'uniform_scheduling_office_id' => "required",
            'uniform_scheduling_office_timing_id' => "required",
            'booked_date' => "date|date_format:Y-m-d|after_or_equal:today",
            // 'g-recaptcha-response' => 'required|recaptcha',
        ];

        $gender = $this->request->get("gender");
        if ($gender == 1) {
            $dimensions = UniformSchedulingMeasurementPoints::whereNotIn("id", [6])->get();
        } else {
            $dimensions = UniformSchedulingMeasurementPoints::get();
        }
        foreach ($dimensions as $dimension) {
            $rules["point_value_" . $dimension->id] =  "required";
        }

        return $rules;
    }

     /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        $message = [];
        $message = [
            'uniform_scheduling_office_id.required' => 'Office is required.',
            'uniform_scheduling_office_timing_id.required' => 'Service is required.',
            'booked_date.required' => 'Booking date is required.',
            // 'g-recaptcha-response.required' => 'Please verify that you are not a robot',
        ];

        $gender = $this->request->get("gender");
        if ($gender == "male") {
            $dimensions = UniformSchedulingMeasurementPoints::whereNotIn("id", [6])->get();
        } else {
            $dimensions = UniformSchedulingMeasurementPoints::get();
        }
        foreach ($dimensions as $dimension) {
            $message["point_value_" . $dimension->id . '.required'] =  $dimension->name . " measure is Required";
        }
        return $message;
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
