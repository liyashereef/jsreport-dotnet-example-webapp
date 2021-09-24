<?php

namespace Modules\Recruitment\Http\Requests;

class RecRateExperienceLookupRequest extends Request
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
            'experience_ratings' => "bail|required|max:255|unique:mysql_rec.rec_rate_experience_lookups,experience_ratings,{$id},id,deleted_at,NULL",
            'score' => "bail|nullable|integer|min:1|max:100000|unique:mysql_rec.rec_rate_experience_lookups,score,{$id},id,deleted_at,NULL",
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
            'experience_ratings.required' => 'Rating is required.',
            'experience_ratings.unique' => 'Rating is already added.',
            'experience_ratings.max' => 'Rating should not exceed 255 characters.',
        ];
    }
}
