<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
class IdsPaymentMethodsRequest extends Request
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
            'short_name' => "required|unique:ids_payment_methods,short_name,{$id},id,deleted_at,NULL",
            'full_name' => "required|unique:ids_payment_methods,full_name,{$id},id,deleted_at,NULL",
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
            'short_name.required' => 'Short name is required.',
            'full_name.required' => 'Full name is required.',
        ];
    }

}
