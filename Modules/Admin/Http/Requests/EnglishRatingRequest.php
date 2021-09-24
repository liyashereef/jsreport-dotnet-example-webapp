<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnglishRatingRequest extends FormRequest
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
            'english_ratings' => "bail|required|max:255|unique:english_rating_lookups,english_ratings,{$id},id,deleted_at,NULL",
            'score' => "bail|required|numeric||min:1|max:100000|unique:english_rating_lookups,score,{$id},id,deleted_at,NULL",
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
            'english_ratings.required' => 'Rating is required.',
            'english_ratings.unique' => 'This Rating is already added.',
            'english_ratings.max' => 'The Rating should not exceed 255 characters.',
            'score.required' => 'Score is required.',
            'score.unique' => 'This Score is already added.',
            'score.numeric' => 'Numeric values only allowed.',
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
