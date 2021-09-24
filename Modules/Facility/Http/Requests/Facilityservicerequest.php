<?php

namespace Modules\Facility\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Facilityservicerequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'facility' => 'required',
            'description' => 'nullable|max:255',
            'booking_interval' => 'required|min:0.25|numeric|max:24',
            'tolerance_perslot' => 'required|numeric|max:100',

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
        return [
            'facility.required' => 'Fill in category name',
            'booking_interval.required' => 'Fill in booking Interval',
            'tolerance_perslot.required' => 'Fill in tolerance',
            'tolerance_perslot.max'=> 'Maximum occupancy per slot cannot exceed 100 ',
        ];
    }
}
