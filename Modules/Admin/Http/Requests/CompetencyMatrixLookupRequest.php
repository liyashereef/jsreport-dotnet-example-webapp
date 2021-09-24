<?php

namespace Modules\Admin\Http\Requests;

class CompetencyMatrixLookupRequest extends Request
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
            'competency' => "bail|required|max:255|unique:competency_matrix_lookups,competency,{$id},id,deleted_at,NULL",
            'definition' => 'bail|required|max:1000',
            'competency_matrix_category_id' => 'min:1',
            'behavior' => 'bail|required|max:1000',
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
            'competency.required' => 'Competency is required.',
            'definition.required' => 'Definition is required.',
            'competency_matrix_category_id.required' => 'Choose one category.',
            'competency_matrix_category_id.min' => 'Choose one category.',
            'behavior.required' => 'Behavior is required.',
        ];
    }

}
