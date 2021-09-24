<?php

namespace Modules\Admin\Http\Requests;

class IndustrySectorRequest extends Request
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
            'industry_sector_name' => "bail|required|max:255|unique:industry_sector_lookups,industry_sector_name,{$id},id,deleted_at,NULL",
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
            'industry_sector_name.required' => 'Industry Sector is required.',
            'industry_sector_name.unique' => 'This Industry Sector is already added.',
            'industry_sector_name.max' => 'The Industry Sector should not exceed 255 characters.',
        ];
    }

}
