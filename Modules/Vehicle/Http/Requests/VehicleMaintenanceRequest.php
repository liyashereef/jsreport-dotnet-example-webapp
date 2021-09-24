<?php

namespace Modules\Vehicle\Http\Requests;

class VehicleMaintenanceRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
       // $vehicle_odometer =request('vehicle_odometer');

            $vehicle_rules=[ 'type_id'=>"bail|required|unique_multiple:vehicle_maintenance_records,type_id,vehicle_id,service_kilometre,service_date"];

    
       $rules = [
         //   'service_kilometre' => 'bail|required|gte:'.$vehicle_odometer.'|digits_between:1,6',
            'service_kilometre' => 'bail|required|digits_between:1,6',
            'service_date' => 'bail|required|date|before_or_equal:today',
            'total_amount'=>'bail|required|numeric|max:999999',
            'invoice'=>'bail|mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,application/vnd.ms-office,application/excel,application/vnd.ms-excel,application/vnd.msexcel,application/octet-stream,application/zip'

        ];
        $rule=array_merge($vehicle_rules,$rules);
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
            'service_kilometre.digits_between'=>'Odometer reading should be between 1 and 6 digits',
            'service_kilometre.required'=>'Odometer reading is required',
         //   'service_kilometre.gte'=>'Odometer reading is less than current odometer reading',
            'service_date.required'=>'Service date is required',
            'service_date.date'=>'Invalid service date',
            'service_date.before_or_equal'=> 'Service date should not be a future date.',
            'invoice.mimetypes'=>'Please upload a file of specified format',
            'total_amount.max'=>'Total charges should be maximum 6 digits'
            
        ];
    }
}
