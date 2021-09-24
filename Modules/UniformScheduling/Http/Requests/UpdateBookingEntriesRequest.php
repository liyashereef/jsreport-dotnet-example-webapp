<?php

namespace Modules\UniformScheduling\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Admin\Models\UniformSchedulingMeasurementPoints;

class UpdateBookingEntriesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'id' => "required",
            'notes'=>"max:300"
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
            'id.required' => 'Booking Id is required.'
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
