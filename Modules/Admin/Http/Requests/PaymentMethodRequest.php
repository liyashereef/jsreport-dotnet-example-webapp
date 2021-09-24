<?php

namespace Modules\Admin\Http\Requests;

class PaymentMethodRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $paymentmethodtitle = request('paymentmethodtitle');
        return $rules = [
            'paymentmethodtitle' => "required",       
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
            'paymentmethodtitle.required' => 'Payment Method is required.',
        ];
    }

}
