<?php

namespace Modules\Expense\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CostCenterLookupRequest extends FormRequest
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
            'center_number' => "bail|required|max:255|unique:expense_cost_center_lookups,center_number,{$id},id,deleted_at,NULL",
            'center_owner_id' =>  "bail|required",
            'center_senior_manager_id' =>  "bail|required",
            'region_id' =>  "bail|required",
            'description' => "bail|max:255"
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
             'center_number.required' => 'Cost center number is required.',
             'center_owner_id.unique' =>  'This center number is already added.',
             'center_owner_id.required' => 'Cost owner is required.',
             'center_senior_manager_id.required' => 'Cost senior manager is required.',
             'region_id.required' => 'Cost region is required.',
             'description.max' => 'The Description should not exceed 255 characters.',
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
