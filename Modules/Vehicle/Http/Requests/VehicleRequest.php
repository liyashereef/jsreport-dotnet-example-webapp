<?php

namespace Modules\Vehicle\Http\Requests;

use Modules\Vehicle\Rules\UniqueTypeforVehicle;
use Illuminate\Validation\Rule;
use Validator;

class VehicleRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $data_type=request('data_type');
        $type=request('type');
        $vehicle_id=request('vehicle_id');
        $odometer_reading=request('odometer_reading');
        $rules = [
            'vehicle_id' => "bail|required|not_in:0",
            'odometer_reading' => "bail|required|max:999999|numeric",          
            //'service.*' => 'bail|required',
            'type.*'=>['bail', 'required','not_in:0', new UniqueTypeforVehicle($type)],
            'interval.*' => 'bail|required|between:1,6|not_in:0',
            'notes'=> 'bail|max:255',
        ];
        foreach ($data_type as $key => $service) {
            if($service=='km')
            $service_rules['service.' . $key ] = 'bail|required|numeric|lte:'.$odometer_reading.'';
            else
            $service_rules['service.' . $key ] = 'bail|required|date|before_or_equal:today';
            $rule=array_merge($service_rules,$rules);

        } 
         return $rule;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function messages()
    {
        return [
            'vehicle_id.required' => 'Please choose vehicle.',
            'vehicle_id.not_in' => 'Please choose vehicle.',
            'type.*.required' => 'Please choose type.',
            'type.*.not_in'=>'Please choose type.',
            'interval.*.not_in'=>'Interval should not be 0.',
            'service.*.required' => 'Enter latest service kilometer/date.',
            'service.*.lte'=>'Latest service km is greater than current odometer reading',
            'service.*.before_or_equal' => 'Service date must be a date today or before.',
            'service.*.between' => 'Latest service kilometer/date should be between 1 and 5 digits.',
            'interval.*.between' => 'Service interval should be maximum 6 digits.',
             'service.*.between' => 'Interval should be maximum 6 digits.',
            'interval.*.required' => 'Enter service interval.',
            'odometer_reading.required'=>'Please enter Odometer reading. ',
            'odometer_reading.max'=>'Odometer Reading should not exceed 6 digits',
            'odometer_reading.numeric'=>'Odometer Reading should be number',
            'notes.required'=>'Enter notes'
        ];
    }
}
