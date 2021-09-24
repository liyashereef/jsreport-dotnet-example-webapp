<?php

namespace Modules\Recruitment\Http\Requests;

class RecCompetencyMatrixCategoryLookupRequest extends Request
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
            'category_name' => "required|unique:mysql_rec.rec_competency_matrix_category_lookups,category_name,{$id},id,deleted_at,NULL",
            'short_name' => 'required',
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
            'category_name.required' => 'This field is required.',
            'short_name.required' => 'This field is required.',
        ];
    }
}
