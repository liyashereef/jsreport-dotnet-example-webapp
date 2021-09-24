<?php

namespace Modules\Recruitment\Http\Requests;

class RecProcessTabRequest extends Request
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
            'system_name' => "bail|required|max:255|unique:mysql_rec.rec_process_tabs,system_name,{$id},id,deleted_at,NULL",
            'display_name' => "bail|required|unique:mysql_rec.rec_process_tabs,display_name,{$id},id,deleted_at,NULL",
            'order' => "bail|required|integer|min:1|unique:mysql_rec.rec_process_tabs,order,{$id},id,deleted_at,NULL",
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
            'system_name.required' => 'System Name is required.',
            'system_name.unique' => 'This System Name is already added.',
            'display_name.unique' => 'This  Display Name is already added.',
            'display_name.required' => 'Display Name is required.',
            'order.unique' => 'This Order is already added.',
            'order.required' => 'Order is required.',
        ];
    }
}
