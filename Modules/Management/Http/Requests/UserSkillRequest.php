<?php

namespace Modules\Management\Http\Requests;

use Modules\Admin\Http\Requests\UserRequest;

class UserSkillRequest extends UserRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $skill_row_ids = request('skill-row-no');
        $rules = [];
        if ($skill_row_ids != null) {
             $skillArr=array();
            foreach ($skill_row_ids as $id) {
                $skill_rules = [
                    'skill_' . $id => 'bail|required|not_in:'.implode(',', $skillArr),
                    'skillvalue_' . $id => 'bail|required_with:skill_' . $id . '|required',
                ];
                  $skillArr[]=request('skill_'.$id);
                $rules = array_merge($rules, $skill_rules);
            }
        }
        return $rules;
    }
    public function messages()
    {
        $id = request('id');
        $skill_row_ids = request('skill-row-no');
        $message = [];
        if ($skill_row_ids != null) {
            foreach ($skill_row_ids as $id) {
                $skill_rules = [
                    'skill_' . $id . '.required' => 'Please choose a skill.',
                    'skillvalue_' . $id . '.required' => 'Please select any value.',
                    'skillvalue_' . $id . '.required_with' => 'Please select any value.',
                     'skill_' . $id . '.not_in' => 'Please choose different skill.',
                ];
                $message = array_merge($message, $skill_rules);
            }
        }
        return $message;
    }
}
