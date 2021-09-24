<?php

namespace Modules\Recruitment\Http\Requests;

class RecTrainingRequest extends Request
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
            'training' => "bail|required|max:255|unique:mysql_rec.rec_training_timing_lookups,training,{$id},id,deleted_at,NULL",
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
            'training.required' => 'Training Type is required.',
            'training.unique' => 'This Training Type is already added.',
            'training.max' => 'The training type should not exceed 255 characters.',
        ];
    }
}
