<?php

namespace Modules\Recruitment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecCompetencyMatrixRatingLookupRequest extends Request
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
            'rating' => "bail|required|max:255|unique:mysql_rec.rec_competency_matrix_rating_lookups,rating,{$id},id,deleted_at,NULL",
            'order_sequence' => "bail|nullable|integer|min:1|max:100000|unique:mysql_rec.rec_competency_matrix_rating_lookups,order_sequence,{$id},id,deleted_at,NULL",
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
            'rating.required' => 'Rating is required.',
            'rating.unique' => 'Rating name already exists',
        ];
    }
}
