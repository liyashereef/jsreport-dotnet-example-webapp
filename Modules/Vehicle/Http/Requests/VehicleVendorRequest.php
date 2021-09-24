<?php

namespace Modules\Vehicle\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleVendorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        return $rules = [
            'vehicle_vendor' => "bail|required|max:255|unique:vehicle_vendor_lookups,vehicle_vendor,{$id},id,deleted_at,NULL",
        ];
    }

        /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'vehicle_vendor.required' => 'Vendor name is required.',
            'vehicle_vendor.unique' => 'This vendor name is already added.',
            'vehicle_vendor.max' => 'This vendor name should not exceed 255 characters.',
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
