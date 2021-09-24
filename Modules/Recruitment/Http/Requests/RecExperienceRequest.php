<?php

namespace Modules\Recruitment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecExperienceRequest extends FormRequest
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
            'experience' => "bail|required|max:255|unique:mysql_rec.rec_experience_lookups,experience,{$id},id,deleted_at,NULL",
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
            'experience.required' => 'Experience is required.',
            'experience.unique' => 'This Experience is already added.',
            'criteria.max' => 'This Experience should not exceed 255 characters.',
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
