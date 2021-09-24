<?php

namespace Modules\Admin\Http\Requests;

class IncidentRecipientRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $array_size = sizeof(request('email'));
        $pos=request('pos');
        $rules=[];
        //for ($i = 0; $i < $array_size; $i++) {
        foreach ($pos as $i => $value) {
            $priority_rules = [
               'email.'.$i=>['required','email'],
               'high.' . $i => ['bail', 'required_without_all:low.'.$i.',medium.'.$i],
               'medium.' . $i => ['bail', 'required_without_all:high.'.$i.',low.'.$i],
               'low.' . $i => ['bail', 'required_without_all:high.'.$i.',medium.'.$i],
            ];
            $rules = array_merge($rules, $priority_rules);
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
           'email.*.email' => 'Email format is wrong',
           'email.*.required' => 'Email is required',
           'high.*.required_without_all'=>'Please select this checkbox for high priority',
           'medium.*.required_without_all'=>'Please select this checkbox for medium priority',
           'low.*.required_without_all'=>'Please select this checkbox for low priority',

        ];
    }
}
