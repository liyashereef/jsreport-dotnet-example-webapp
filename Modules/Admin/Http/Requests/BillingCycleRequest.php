<?php

namespace Modules\Admin\Http\Requests;

class BillingCycleRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $ratechangetitile = request();
        return $rules = [
            'billingcycletitle' => "required",       
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
            'billingcycletitle.required' => 'Billing Frequency Title is required.',
        ];
    }

}
