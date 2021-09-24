<?php

namespace Modules\Recruitment\Http\Requests;

class RecMatchScoreCriteriaRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $position=request('position');
        $weight=request('weight');
        $criteria_rules = [];
        $rules = [
            'weight'=>'sum:100'];

        foreach ($position as $key => $index) {
            $criteria_rules = [
               'criteria_name.' . $index => ['bail', 'required'],
               'type_id.' . $index => ['bail', 'required','not_in:0'],
               'weight.' . $index => ['bail', 'required'],
             
            ];
            $rules = array_merge($rules, $criteria_rules);
            # code...
        }
        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
             'type_id.*.not_in' => 'Choose any type.',
             'criteria_name.*.required' => 'Criteria field is required.',
             'weight.*.required' => 'Enter weight percentage',
        ];
    }
}
