<?php

namespace Modules\Vehicle\Http\Requests;

class VehicleListRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $year=request('year');
        return $rules = [
            'make' => "bail|required|max:20",
            'number' => "bail|required|max:20|unique:vehicles,number,{$id},id,deleted_at,NULL",
            'year' => 'bail|required|digits:4|integer|min:1900|max:'.(date("Y")),
            'model' => 'bail|required|max:20',
            'odometer_reading'=> 'bail|required|digits_between:1,6',
            'region'=>'bail|required',
            'vin'=>"bail|required|max:17|unique:vehicles,vin,{$id},id,deleted_at,NULL",
            'description'=>'bail|max:500',
            'purchasing_date'=>'bail|required|date_format:"Y-m-d"|before_or_equal:today|year_greater_than:year'
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
            'make.required' => 'Vehicle name is required.',
            'make.unique' => 'Vehicle name is already added.',
            'make.max' => 'The vehicle name should not exceed 20 characters.',
            'number.required' => 'Vehicle number is required.',
            'number.unique' => 'Vehicle number is already added.',
            'number.max' => 'The vehicle number should not exceed 20 characters.',
            'model.required'=> 'Vehicle model is required.',
            'model.max' => 'The vehicle number should not exceed 20 characters.',
            'year.required'=> 'Vehicle year is required.',
            'odometer_reading.digits_between'=>'Odometer reading should be between 1 and 6 digits',
            'purchasing_date.year_greater_than'=>'Purchasing date should be greater than manufacturing year'
        ];
    }
}
