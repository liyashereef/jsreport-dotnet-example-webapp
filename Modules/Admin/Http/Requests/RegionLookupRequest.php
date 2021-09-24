<?php

namespace Modules\Admin\Http\Requests;

class RegionLookupRequest extends Request
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
            'region_name' => "bail|required|max:255|unique:region_lookups,region_name,{$id},id,deleted_at,NULL",
            'region_description' => "bail|required|max:1000",
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
            'region_name.required' => 'Region name is required.',
            'region_name.unique' => 'This region is already added.',
            'region_name.max' => 'The region should not exceed 255 characters.',
        ];
    }

}
