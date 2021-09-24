<?php

namespace Modules\Recruitment\Http\Requests;

class RecTrainingTimingRequest extends Request
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
            'timings' => "bail|required|max:255|unique:mysql_rec.rec_timing_lookups,timings,{$id},id,deleted_at,NULL",
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
            'timings.required' => 'Training Time is required.',
            'timings.unique' => 'This Training Time is already added.',
            'timings.max' => 'The Training Time should not exceed 255 characters.',
        ];
    }
}
