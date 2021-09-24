<?php

namespace Modules\Facility\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FacilityBookingFilterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'facility_id' => 'required',
            'single_service_facility' => 'required',
            'facility_service_id' => 'required_if:single_service_facility,0',
            'booking_date' => 'required|date|date_format:Y-m-d|after_or_equal:today',
        ];
    }

    public function messages()
    {
        return [
            'facility_id.required' => 'Facility is mandatory.',
            'facility_service_id.required_if' => 'Facility service is mandatory.',
            'booking_date.required' => 'Booking date is a mandatory field.', 
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
