<?php

namespace Modules\Sensors\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SensorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        return [
            'name' => "bail|required|max:200|unique:sensors,name,{$id},id,deleted_at,NULL",
            'nod_mac' => "bail|required|max:200|unique:sensors,nod_mac,{$id},id,deleted_at,NULL",
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
            'name.required' => 'Sensor name is required.',
            'name.unique' => 'Sensor name is already added.',
            'name.max' => 'Sensor name should not exceed 200 characters.',
            'nod_mac.required' => 'Nod Mac is required.',
            'nod_mac.unique' => 'Nod Mac is already added.',
            'nod_mac.max' => 'The Node Mac should not exceed 200 characters.'
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
