<?php

namespace Modules\Vehicle\Http\Requests;

class VehicleMaintenanceTypeRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $type=request('type');
        
        $rules = [
            'category_id' => "bail|required",
            'name'=>"bail|max:191|required|unique:vehicle_maintenance_types,name,{$id},id,deleted_at,NULL",
            'type'=>"bail|required",
        ];
        //type==1 indicates its a kilometer field and type==2 denoting its date
        if($type==1)
        {
            $critical_array=[ 'critical_after_km'=>"bail|max:999999999|required|integer|min:0"];
        }
        else
        {
            $critical_array=[  'critical_after_days'=>"bail|max:999999999|required|integer|min:0"];
        }
        $rule=array_merge($critical_array,$rules);
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
            'category_id.required' => 'Category  is required.',
            'name.required' => 'Name is required.',
            'name.max'=>'Name should not exceed 191 characters',
            'critical_after_km.required'=>'Critical after kilometer is required',
            'critical_after_km.max'=>'Critical after kilometer should not exceed 9 digits',
            'critical_after_km.min'=>'Critical after kilometer should be minimum 0',
            'critical_after_days.required'=>'Critical after days is required',
             'critical_after_days.max'=>'Critical after days should not exceed 9 digits',
            'critical_after_days.min'=>'Critical after days should be minimum 0',
            'type.required'=>'Type is required',
        ];
    }
}
