<?php

namespace Modules\Hranalytics\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Admin\Models\UniformSchedulingMeasurementPoints;

class UniformEntryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $gender = $this->request->get("gender");
        if ($gender == "male") {
            $dimensions = UniformSchedulingMeasurementPoints::whereNotIn("id", [6])->get();
        } else {
            $dimensions = UniformSchedulingMeasurementPoints::get();
        }
        $rulesarray = [];
        foreach ($dimensions as $dimension) {
            # code...
            $rulesarray["uniformcontrol-" . $dimension->id] =  "required";
        }
        //dd(array_values($rulesarray));
        // dd($this->request);

        return $rules = $rulesarray;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        $gender = $this->request->get("gender");
        if ($gender == "male") {
            $dimensions = UniformSchedulingMeasurementPoints::whereNotIn("id", [6])->get();
        } else {
            $dimensions = UniformSchedulingMeasurementPoints::get();
        }
        $messagearray = [];
        foreach ($dimensions as $dimension) {
            # code...
            $messagearray["uniformcontrol-" . $dimension->id . '.required'] =  $dimension->name . " measure is Required";
        }
        return $messagearray;
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
