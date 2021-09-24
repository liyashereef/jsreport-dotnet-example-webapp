<?php

namespace Modules\Facility\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FacilityServiceUpdateRequest extends FormRequest
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
            'booking_interval' => 'required|min:0.25|numeric|max:100',
            'tolerance_perslot' => 'required|numeric|max:100',
            'restrict_booking' => 'required',
            'active' => 'required',
            
            
        ];
    }

    public function messages()
    {
        return [
            'facility.required' => 'Fill in category name',
            'booking_interval.required' => 'Fill in booking interval',
            'tolerance_perslot.required' => 'Fill in maximum occupancy per slot',
            'tolerance_perslot.max'=> 'Maximum occupancy per slot cannot exceed 100 ',
            'weekend_booking.required' => 'Choose weekend booking',
            'restrict_booking.required' => 'Choose restrict booking',
            'active.required' => 'Choose status',
            

            
        ];
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolstart_time
     */
    public function authorize()
    {
        return true;
    }
}
