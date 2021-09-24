<?php

namespace Modules\Admin\Http\Requests;

class CandidateExperienceRequest extends Request
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
            'experience' => "bail|required|max:255|unique:experience_lookups,experience,{$id},id,deleted_at,NULL",
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

}
