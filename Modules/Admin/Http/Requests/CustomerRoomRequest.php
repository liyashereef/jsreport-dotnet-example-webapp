<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRoomRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $customer_id = request('customer_id');
        $severity_id = request('severity_id');
        $rules = [
            'name' => "bail|required|max:200|unique:customer_rooms,name,{$id},id,customer_id,{$customer_id},severity_id,{$severity_id},deleted_at,NULL",
        ];
        if ($id == null) {
            $other_rules = [
                'customer_id' =>  "bail|required",
                'severity_id' =>  "bail|required|not_in:0",
            ];
            $rules = array_merge($rules, $other_rules);
        }
        return $rules;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function messages()
    {
        return [
            'name.required' => 'Room name is required.',
            'name.max' => 'Room name should not exceed 200 characters.',
            'name.unique' => 'Room name id has already been taken.',
            'customer_id.required' => 'Customer name is required.',
            'severity_id.not_in' => 'Severity is required.',
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
