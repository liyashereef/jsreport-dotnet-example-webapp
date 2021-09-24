<?php

namespace Modules\Facility\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FacilityRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $max_booking = \Request::get('maxbooking_perday');
        $single_service_facility = \Request::get('single_service_facility');
        if ($single_service_facility == "yes") {

            $condition = [
                'facility' => 'required',
                'description' => 'nullable|string|max:255',
                'customer_id' => 'required',
                'single_service_facility' => 'required',
                'maxbooking_perday' => 'required|min:0.25|numeric|max:24',
                'booking_interval' => 'required|min:0.25|max:' . $max_booking . '|numeric',
                'tolerance_perslot' => 'required|numeric|max:100',
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
                'booking_window' => 'required|numeric|min:1',
                'weekend_booking' => 'required',
                'weekend_start_time' => 'nullable|required_if:weekend_booking,1',
                'weekend_end_time' => 'nullable|required_if:weekend_booking,1|after:weekend_start_time',
            ];
        } else {
            $condition = [
                'facility' => 'required',
                'description' => 'nullable|max:255',
                'customer_id' => 'required',
                'single_service_facility' => 'required',
                'maxbooking_perday' => 'required|min:0.25|numeric|max:24',
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
                'booking_window' => 'required|min:1',
                'weekend_booking' => 'required',
                'weekend_start_time' => 'nullable|required_if:weekend_booking,1',
                'weekend_end_time' => 'required_if:weekend_booking,1|nullable|after:weekend_start_time',
            ];
        }
        return $condition;
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
            'customer_id.required' => 'Choose customer',
            'maxbooking_perday.required' => 'Fill in maximum booking per day',
            'maxbooking_perday.max' => 'Maximum booking per day may not exceed 24 hours',
            'booking_interval.required' => 'Fill in booking interval',
            'tolerance_perslot.required' => 'Fill in maximum occupancy per slot',
            'start_time.required' => 'Choose start time  ',
            'end_time.required' => 'Choose end time ',
            'end_time.after' => 'The end time must be greater than start time',
            'booking_window.required' => 'Fill in booking window ',
            'weekend_booking.required' => 'Choose weekend booking',
            'weekend_start_time.required_if' => 'Choose start time  ',
            'weekend_end_time.required_if' => 'Choose end time ',
            'weekend_start_time.after' => 'The end time must be greater than start time',
            'tolerance_perslot.max' => 'Maximum occupancy per slot cannot exceed 100 ',
        ];
    }
}
