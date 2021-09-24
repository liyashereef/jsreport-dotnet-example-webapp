<?php

namespace Modules\Facility\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FacilityUpdateRequest extends FormRequest
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
        if($single_service_facility=="yes"){

            $condition = [
                'facility' => 'required',
                'customer_id' => 'required',
                'description' => 'nullable|max:255',
                'single_service_facility'=>'required',
                'maxbooking_perday' => 'required|min:0.25|numeric|max:24',
                'slot_interval' => 'required|min:0.25|max:'.$max_booking.'|numeric',
                'tolerance_perslot' => 'required|numeric|max:100',
                'booking_window' => 'required|numeric|min:1',
                'weekend_booking' => 'required',
            ];
        }else{
            $condition = [
                'facility' => 'required',
                'customer_id' => 'required',
                'description' => 'nullable|max:255',
                'single_service_facility'=>'required',
                'maxbooking_perday' => 'required|min:0.25|numeric|max:24',
                'booking_window' => 'required|numeric|min:1',
                'weekend_booking' => 'required',
            ];
        }
        return $condition;
    }
    public function messages()
    {
        return [
            'facility.required' => 'Fill in facility name',
            'customer_id.required' => 'Choose customer',
            'maxbooking_perday.required' => 'Fill in maximum booking per day',
            'maxbooking_perday.max' => 'Maximum booking per day may not exceed 24 hours',
            'slot_interval.required' => 'Fill in booking interval',
            'tolerance_perslot.required' => 'Fill in maximum occupancy per slot',
            'start_time.required' => 'Choose start time  ',
            'end_time.required' => 'Choose end time ',
            'booking_window.required' => 'Fill in booking window ',
            'weekend_booking.required' => 'Choose weekend booking',
            'tolerance_perslot.max'=> 'Maximum occupancy per slot cannot exceed 100 ',



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
