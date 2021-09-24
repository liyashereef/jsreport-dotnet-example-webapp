<?php

namespace Modules\Vehicle\Http\Requests;

class VehiclePendingMaintenanceRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $rules = [
            'vendor_id' => 'bail|required',
            'service_kilometre' => 'bail|required|digits_between:1,6',
            'service_date' => 'bail|required|date|before_or_equal:today',
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
            'vendor_id.required'=>'Vendor name is required',
            'service_kilometre.digits_between'=>'Odometer reading should be between 1 and 6 digits',
            'service_kilometre.required'=>'Odometer reading is required',
            'service_date.required'=>'Service date is required',
            'service_date.date'=>'Invalid service date',
            'service_date.before_or_equal'=> 'Service date should not be a future date.'

        ];
    }
}
