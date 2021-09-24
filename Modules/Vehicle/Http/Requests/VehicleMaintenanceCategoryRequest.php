<?php

namespace Modules\Vehicle\Http\Requests;

class VehicleMaintenanceCategoryRequest extends Request
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
            'category_name' => "bail|required|max:255|unique:vehicle_maintenance_categories,category_name,{$id},id,deleted_at,NULL",
            'tax'=>"bail|required|numeric|between:0,100"
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
            'category_name.required' => 'Category name is required.',
            'category_name.unique' => 'Category name is already added.',
        ];
    }
}
