<?php

namespace Modules\ProjectManagement\Http\Requests;

use Modules\ProjectManagement\Rules\SumOfFields;

class TaskRatingRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $deadline_weightage = request('deadline_weightage');
        $value_add_weightage = request('value_add_weightage');
        $initiative_weightage = request('initiative_weightage');
        $commitment_weightage = request('commitment_weightage');
        $complexity_weightage = request('complexity_weightage');
        $efficiency_weightage = request('efficiency_weightage');

        return $rules = [
            'rating_notes' => ['required','max:250'],
            'deadline_rating_id' => ['required'],
            'value_add_rating_id' => ['required'],
            'initiative_rating_id' => ['required'],
            'commitment_rating_id' => ['required'],
            'complexity_rating_id' => ['required'],
            'efficiency_rating_id' => ['required'],
            'deadline_weightage' => ['required'],
            'value_add_weightage' => ['required'],
            'initiative_weightage' => ['required'],
            'commitment_weightage' => ['required'],
            'complexity_weightage' => ['required'],
            'efficiency_weightage' => ['required',new SumOfFields($deadline_weightage, $value_add_weightage, $initiative_weightage, $commitment_weightage, $complexity_weightage, $efficiency_weightage)]
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
            'rating_notes.required' => 'Notes is required',
            'rating_notes.max' => 'Notes should not exceed 250 characters.',
            'deadline_rating_id.required' => 'Deadline rating is required',
            'value_add_rating_id.required' => 'Value Add rating is required',
            'initiative_rating_id.required' => 'Initiative rating is required',
            'commitment_rating_id.required' => 'Commitment rating is required',
            'complexity_rating_id.required' => 'Complexity rating is required',
            'efficiency_rating_id.required' => 'Efficiency rating is required',
            'deadline_weightage.required' => 'Deadline weightage is required',
            'value_add_weightage.required' => 'Value Add weightage is required',
            'initiative_weightage.required' => 'Initiative weightage is required',
            'commitment_weightage.required' => 'Commitment weightage is required',
            'complexity_weightage.required' =>  'Complexity weightage is required',
            'efficiency_weightage.required' =>  'Efficiency weightage is required',

        ];
    }
}
