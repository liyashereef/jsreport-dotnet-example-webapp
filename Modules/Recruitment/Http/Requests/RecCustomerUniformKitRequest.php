<?php

namespace Modules\Recruitment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecCustomerUniformKitRequest extends FormRequest
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

        return [
            'customer_id' => "bail|required|not_in:0",
            'kit_name' => "bail|required",
            'quantity.*' => "bail|required|min:1|max:100",
            'item_id.*' => "bail|required|not_in:0|distinct",
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
            'customer_id.not_in' => 'Please choose any customer',
            'kit_name.required'=>'Kit Name is required',
            'kit_name.unique'=>'The kit name has already been taken.',
            'quantity.*.required' => 'Quantity is required',
            'item_id.*.required' => 'Item type is required',
            'item_id.*.not_in' => 'Please choose any item type',
            'item_id.*.distinct' => 'Item type already exists',

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
